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
        $username = "root";
        $password = "";
        $dbname = "escolamusica";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }

        // Insere os dados na tabela "usuarios"
        $sql = "INSERT INTO usuarios (login, senha) VALUES ('$login', '$senha')";

        if ($conn->query($sql) === TRUE) {
            echo "Cadastro realizado com sucesso!";
            // Redireciona para a página de login
            header("Location: login.php");
            exit;
        } else {
            echo "Erro ao cadastrar: " . $conn->error;
        }

        $conn->close();
    }
}
?>
