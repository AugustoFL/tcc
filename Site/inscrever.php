<?php    
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die('Você precisa estar logado para se inscrever.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Usando o login armazenado na sessão para buscar o ID do usuário no banco de dados
    $cpf_usuario = $_SESSION['user'];  // Supondo que você armazena o CPF do usuário na sessão
    $id_curso = $_POST['course_id'];
    $tipo_inscricao = $_POST['tipo_inscricao'];

    // Conexão com o banco de dados
    $host = 'localhost';
    $dbname = 'escolamusica';
    $user_db = 'root';
    $pass_db = '';

    $conn = new mysqli($host, $user_db, $pass_db, $dbname);

    if ($conn->connect_error) {
        die("Erro ao conectar: " . $conn->connect_error);
    }

    // Buscar o ID do usuário com base no CPF ou login
    $stmt = $conn->prepare("SELECT login FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $cpf_usuario);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    // Verificar se o ID do usuário foi encontrado
    if (!$id_usuario) {
        die('Usuário não encontrado.');
    }

    // Inserir a inscrição no banco de dados
    $stmt = $conn->prepare("INSERT INTO inscricoes (id_curso, cpf, tipo_inscricao) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_curso, $id_usuario, $tipo_inscricao);

    if ($stmt->execute()) {
        echo "Inscrição realizada com sucesso!";
    } else {
        echo "Erro ao realizar a inscrição.";
    }

    $stmt->close();
    $conn->close();
}
?>
