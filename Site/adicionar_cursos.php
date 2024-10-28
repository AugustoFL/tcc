<?php
session_start();
require_once 'conexao.php';

function verificarAcesso() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.html");
        exit;
    }
}

verificarAcesso();

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';
if ($tipo_usuario !== 'funcionario') {
    header("Location: index2.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $horario = $_POST['horario'];
    $tipo = $_POST['tipo'];
    
    $stmt = $conn->prepare("INSERT INTO cursos (nome, descricao, horario, tipo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $descricao, $horario, $tipo);
    
    if ($stmt->execute()) {
        header("Location: gerenciar_cursos.php?msg=Curso+adicionado+com+sucesso");
    } else {
        header("Location: gerenciar_cursos.php?msg=Erro+ao+adicionar+curso");
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Curso</title>
    <style>
        /* Estilo global para manter consistência */
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

        .sidebar h2, .sidebar h3 {
            color: #fafafa;
            margin-top: 0;
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
            margin-left: 270px;
            padding: 40px;
            background-color: #2b2b2b;
            flex: 1;
            overflow-y: auto;
        }

        h1, h2 {
            color: #fafafa;
        }

        .form-box {
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 500px;
        }

        label, input, textarea {
            display: block;
            width: 100%;
            color: #f1f1f1;
        }

        input, textarea {
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            background-color: #444;
            border: 1px solid #555;
            border-radius: 4px;
            color: #f1f1f1;
        }

        button {
            padding: 10px 20px;
            background-color: #750a67;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #9b0a88;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <h2>Painel</h2>
        <h3>Informações</h3>
        <a href="informacoes_pessoais.php">Informações Pessoais</a>
        <a href="editar_perfil.php">Editar Perfil</a>

        <!-- Se o usuário for funcionário, mostra opções adicionais -->
        <?php if ($tipo_usuario === 'funcionario'): ?>
            <h3>Administração</h3>
            <a href="gerenciar_usuarios.php">Gerenciar Usuários</a>
            <a href="relatorio_func.php">Relatórios</a>
            <a href="gerenciar_cursos.php">Gerenciar Cursos</a>
        <?php endif; ?>
        <br>
        <a href="index2.php" class="logout-btn">Voltar</a>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div class="main-content">
        <h1>Adicionar Curso</h1>
        <div class="form-box">
            <form method="post">
                <label>Nome:</label>
                <input type="text" name="nome" required>
                
                <label>Descrição:</label>
                <textarea name="descricao" required></textarea>
                
                <label>Horário:</label>
                <input type="text" name="horario" required>
                
                <label>Tipo:</label>
                <input type="text" name="tipo" required>
                
                <button type="submit">Adicionar</button>
            </form>
        </div>
    </div>
</body>
</html>
