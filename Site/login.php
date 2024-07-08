<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Validação (você pode adicionar mais validações aqui)
    if (empty($login) || empty($senha)) {
        echo "Preencha todos os campos!";
    } else {
        // Conexão com o banco de dados (substitua pelos seus dados)
        $servername = "localhost";
        $username = "seu_usuario";
        $password = "sua_senha";
        $dbname = "seu_banco_de_dados";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }

        // Consulta para verificar as credenciais
        $sql = "SELECT * FROM usuarios WHERE login = '$login' AND senha = '$senha'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Usuário autenticado com sucesso
            header("Location: index.php");
            exit;
        } else {
            echo "Credenciais inválidas. Tente novamente.";
        }

        $conn->close();
    }
}
?>
