<?php
session_start();

// Verifique se o usuário está logado e se é funcionário
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: login.html");
    exit;
}

// Verifica o tipo de usuário
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';

// Apenas funcionários podem acessar a página de relatórios
if ($tipo_usuario !== 'funcionario') {
    header("Location: painel.php"); // Redireciona para o painel principal se não for funcionário
    exit;
}
require_once 'conexao.php';

// Verifica se o login foi passado como parâmetro na URL
if (!isset($_GET['login'])) {
    header("Location: gerenciar_usuarios.php");
    exit;
}

$login = $_GET['login'];

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_login = $_POST['login'];
    $novo_tipo_usuario = $_POST['tipo_usuario'];

    // Definir a variável @admin_cpf no MySQL com o CPF do administrador logado
    $admin_cpf = $_SESSION['user'];
    $conn->query("SET @admin_cpf = '{$admin_cpf}'");

    // Atualiza o usuário no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET login = ?, tipo_usuario = ? WHERE login = ?");
    $stmt->bind_param("sss", $novo_login, $novo_tipo_usuario, $login);

    if ($stmt->execute()) {
        // Redireciona de volta para a página de gerenciamento com uma notificação
        header("Location: gerenciar_usuarios.php?updated=true");
        exit;
    } else {
        $error = "Erro ao atualizar o usuário.";
    }

    $stmt->close();
}

// Obtém os dados do usuário para preencher o formulário
$stmt = $conn->prepare("SELECT login, tipo_usuario FROM usuarios WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->bind_result($login_atual, $tipo_usuario_atual);
$stmt->fetch();
$stmt->close();
?>

<!-- HTML permanece o mesmo -->


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f;
            color: #f1f1f1;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            background-color: #111;
            padding: 20px;
            width: 250px;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
        }

        .sidebar a {
            display: block;
            color: #f1f1f1;
            text-decoration: none;
            margin: 15px 0;
            padding: 10px;
            background-color: #333;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #750a67;
        }

        .main-content {
            margin-left: 300px;
            padding: 40px;
            background-color: #2b2b2b;
            flex: 1;
            overflow-y: auto;
            height: 100%;
        }

        h1, h2 {
            color: #fafafa;
        }

        .content-box {
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-size: 16px;
        }

        input, select {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #555;
            background-color: #444;
            color: #f1f1f1;
            width: 100%;
        }

        .btn {
            padding: 10px 20px;
            background-color: #750a67;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #9b0a88;
        }

        .error {
            color: #ff5555;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

   <!-- Menu lateral -->
   <div class="sidebar">
        <h2>Painel</h2>
        <h3>Informações</h2>
        <a href="informacoes_pessoais.php">Informações Pessoais</a>
        <a href="editar_perfil.php">Editar Perfil</a>

        <!-- Se o usuário for funcionário, mostra opções adicionais -->
        <?php if ($tipo_usuario === 'funcionario'): ?>
            <h3>Administração</h3>
            <a href="gerenciar_usuarios.php">Gerenciar Usuários</a>
            <a href="relatorio_func.php">Relatórios</a>
        <?php endif; ?>

        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Editar Usuário: <?php echo htmlspecialchars($login_atual); ?></h1>

        <div class="content-box">
            <h2>Atualize as informações do usuário</h2>

            <!-- Formulário de edição -->
            <form method="post">
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($login_atual); ?>" required>

                <label for="tipo_usuario">Tipo de Usuário:</label>
                <select id="tipo_usuario" name="tipo_usuario">
                    <option value="cliente" <?php if ($tipo_usuario_atual === 'cliente') echo 'selected'; ?>>Cliente</option>
                    <option value="funcionario" <?php if ($tipo_usuario_atual === 'funcionario') echo 'selected'; ?>>Funcionário</option>
                </select>

                <!-- Exibir mensagem de erro, se houver -->
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <button type="submit" class="btn">Salvar Alterações</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
