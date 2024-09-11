<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.html");
    exit;
}

// Conecte-se ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escolamusica";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recupera o CPF (login) do usuário da sessão
$login = $_SESSION['user'];

// Inicializa as variáveis
$nome = $email = $rg = "";

// Se o formulário for enviado, atualize os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $rg = $_POST['rg'];
    $nova_senha = $_POST['senha'];

    // Atualiza o nome, e-mail e RG no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, rg = ? WHERE login = ?");
    $stmt->bind_param("ssss", $nome, $email, $rg, $login);
    $stmt->execute();
    $stmt->close();

    // Atualiza as informações na sessão
    $_SESSION['user_name'] = $nome; 
    $_SESSION['user_email'] = $email;

    // Se uma nova senha foi fornecida, atualiza a senha
    if (!empty($nova_senha)) {
        $hashed_password = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE login = ?");
        $stmt->bind_param("ss", $hashed_password, $login);
        $stmt->execute();
        $stmt->close();
    }

    // Upload da imagem do RG, se fornecida
    if (!empty($_FILES['imagem_rg']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["imagem_rg"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verifica se o arquivo é uma imagem válida
        $check = getimagesize($_FILES["imagem_rg"]["tmp_name"]);
        if ($check !== false) {
            // Move o arquivo para o diretório 'uploads'
            if (move_uploaded_file($_FILES["imagem_rg"]["tmp_name"], $target_file)) {
                // Atualiza o caminho da imagem no banco de dados
                $stmt = $conn->prepare("UPDATE usuarios SET imagem_rg = ? WHERE login = ?");
                $stmt->bind_param("ss", $target_file, $login);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Redireciona para a página de informações pessoais
    header("Location: informacoes_pessoais.php");
    exit;
} else {
    // Busca os dados atuais do usuário no banco de dados
    $stmt = $conn->prepare("SELECT nome, email, rg, imagem_rg FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->bind_result($nome, $email, $rg, $imagem_rg);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f;
            color: #f1f1f1;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #2b2b2b;
            padding: 20px;
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #fafafa;
        }
        form {
            margin: 20px 0;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            background-color: #3a3a3a;
            color: #fafafa;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        input[type="submit"] {
            background-color: #750a67;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #a40d8a;
        }
        a {
            color: #750a67;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Editar Perfil</h1>
    <form method="post" action="editar_perfil.php" enctype="multipart/form-data">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($nome); ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="rg">RG</label>
        <input type="text" name="rg" id="rg" value="<?php echo htmlspecialchars($rg); ?>" required>

        <label for="imagem_rg">Imagem do RG</label>
        <input type="file" name="imagem_rg" id="imagem_rg">

        <label for="senha">Nova Senha (deixe em branco para não alterar)</label>
        <input type="password" name="senha" id="senha">

        <input type="submit" value="Salvar Alterações">
    </form>
    <p><a href="painelUsuario.php">Voltar ao Painel</a></p>
</div>

</body>
</html>
