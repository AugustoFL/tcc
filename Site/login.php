<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Remove pontos e hífens do CPF
    $login = str_replace(['.', '-'], '', $login);

    

    // Validação básica
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

        // Consulta usando prepared statements
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        // Verifica se encontrou algum usuário
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verifica se a senha está correta
            if (password_verify($senha, $hashed_password)) {
                header("Location: index2.php");
                exit;
            } else {
                echo "Credenciais inválidas. Tente novamente.";
            }
        } else {
            echo "Usuário não encontrado. Verifique o CPF e tente novamente.";
        }

        $stmt->close();
        $conn->close();
    }
}


?>

