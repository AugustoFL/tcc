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

// Busca os dados do usuário no banco de dados
$stmt = $conn->prepare("SELECT nome, email, rg FROM usuarios WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->bind_result($nome, $email, $rg);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações Pessoais</title>
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
        .info {
            margin: 20px 0;
        }
        .info p {
            font-size: 18px;
            line-height: 1.6;
        }
        a {
            color: #750a67;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            max-width: 200px;
            margin-top: 20px;
            border: 2px solid #fff;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Informações Pessoais</h1>
    <div class="info">
        <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>RG:</strong> <?php echo htmlspecialchars($rg); ?></p>

        <!-- Exibe a imagem do RG, se disponível -->
        <?php if (!empty($imagem_rg)): ?>
            <p><strong>Imagem do RG:</strong></p>
            <img src="<?php echo htmlspecialchars($imagem_rg); ?>" alt="Imagem do RG">
        <?php endif; ?>
    </div>
    <p><a href="painelUsuario.php">Voltar ao Painel</a></p>
</div>

</body>
</html>