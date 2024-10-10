<?php
session_start();

// Verifique se o usuário está logado e se é funcionário
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header("Location: login.html");
    exit;
}

require_once 'conexao.php'; // Arquivo de conexão com o banco de dados

// Verifica o tipo de usuário
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';

// Apenas funcionários podem acessar a página de relatórios
if ($tipo_usuario !== 'funcionario') {
    header("Location: painel.php"); // Redireciona para o painel principal se não for funcionário
    exit;
}
// Obtém todos os usuários da tabela
$query = "SELECT login, tipo_usuario, data_cadastro FROM usuarios";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #444;
        }

        .btn {
            padding: 5px 10px;
            background-color: #750a67;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }

        .btn:hover {
            background-color: #9b0a88;
        }

        .btn-danger {
            background-color: #ff5555;
        }

        .btn-danger:hover {
            background-color: #ff3333;
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
        <h1>Gerenciar Usuários</h1>

        <div class="content-box">
            <h2>Lista de Usuários</h2>
            <table>
                <tr>
                    <th>Login</th>
                    <th>Tipo de Usuário</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['login']; ?></td>
                        <td><?php echo $row['tipo_usuario']; ?></td>
                        <td><?php echo $row['data_cadastro']; ?></td>
                        <td>
                            <a href="editar_usuario.php?login=<?php echo $row['login']; ?>" class="btn">Editar</a>
                            <a href="deletar_usuario.php?login=<?php echo $row['login']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja deletar este usuário?');">Deletar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
