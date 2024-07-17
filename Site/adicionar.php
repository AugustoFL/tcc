<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Validação no lado do servidor
    if (empty($login) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!']);
        exit;
    }

    if (strlen($login) != 11 || strlen($senha) < 5) {
        echo json_encode(['success' => false, 'message' => 'CPF ou senha inválidos.']);
        exit;
    }

    // Conexão com o banco de dados (substitua pelos seus dados)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "escolamusica";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $conn->connect_error]));
    }

    // Usar consulta preparada para evitar injeção SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (login, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $login, $senha);

    if ($stmt->execute() === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

