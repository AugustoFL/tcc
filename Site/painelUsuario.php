<?php
session_start();



// Verifica o tipo de usuário
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
    <style>
        /* Tema escuro */
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f; /* Fundo do tema escuro */
            color: #f1f1f1; /* Texto branco */
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        /* Estilo do menu lateral */
        .sidebar {
            background-color: #111; /* Fundo do menu lateral */
            padding: 20px;
            width: 250px;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
        }

        /* Links do menu */
        .sidebar a {
            display: block;
            color: #f1f1f1;
            text-decoration: none;
            margin: 15px 0;
            padding: 10px;
            background-color: #333; /* Cor dos links */
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        /* Efeito hover nos links */
        .sidebar a:hover {
            background-color: #750a67; /* Cor ao passar o mouse */
        }

        /* Seção de conteúdo principal */
        .main-content {
            margin-left: 300px; /* Alinha o conteúdo à direita do menu */
            padding: 40px;
            background-color: #2b2b2b; /* Fundo do conteúdo */
            flex: 1;
            overflow-y: auto;
            height: 100%;
        }

        /* Estilo dos títulos e seções do painel */
        h1, h2 {
            color: #fafafa;
        }

        .content-box {
            background-color: #333; /* Caixa de conteúdo com tema escuro */
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Estilos específicos para o botão de sair */
        .logout-btn {
            background-color: #ff5555;
            padding: 10px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #ff3333;
        }
    </style>
</head>
<body>

    <!-- Menu lateral -->
    <div class="sidebar">
        <h2>Painel</h2>
        <h3> Informações  </h2>
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
        <h1>Bem-vindo, CPF <?php echo $_SESSION['user']; ?>!</h1>

        <div class="content-box">
            <h2>Informações Gerais</h2>
            <p>Este é o seu painel de usuário. Aqui você pode acessar suas informações e gerenciar sua conta.</p>
        </div>

        <!-- Conteúdo adicional específico para funcionários -->
        <?php if ($tipo_usuario === 'funcionario'): ?>
            <div class="content-box">
                <h2>Administração</h2>
                <p>Aqui você pode gerenciar usuários e visualizar relatórios administrativos.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

