<?php
session_start(); // Inicia a sessão para verificar o login

// Verifica se o usuário está logado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user = $logged_in ? $_SESSION['user'] : null;
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar e Cursos</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            height: 100%;
            overflow-x: hidden;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: rgb(0, 0, 0);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            margin-left: 20px;
            position: relative;
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease, transform 0.3s ease;
            position: relative;
            display: inline-block;
        }

        .navbar ul li:hover a {
            transform: translateY(-3px);
        }

        .navbar ul li a:active {
            transform: translateY(0);
        }

        .login-btn, .user-icon {
            padding: 10px 20px;
            background-color: #fafafa;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover, .user-icon:hover {
            background-color: #750a67;
            color: #fafafa;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            width: 200px;
        }

        .user-dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .user-dropdown a:hover {
            background-color: #f1f1f1;
        }

        .user-menu:hover .user-dropdown {
            display: block;
        }



    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <a href="#home" style="color: #fff; font-weight: bold; text-decoration: none;">Logo</a>
        </div>
        <ul>
            <li><a href="#home">Início</a></li>
            <li><a href="#about">Sobre</a></li>
            <li><a href="#contact">Contato</a></li>
        </ul>

        <!-- Exibe o ícone de usuário se estiver logado, caso contrário exibe o botão de login -->
        <?php if ($logged_in): ?>
            <div class="user-menu">
                <button class="user-icon">👤</button>
                <div class="user-dropdown">
                    <a href="painelUsuario.php">Painel do Usuário</a>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login-btn">
                <a href="login.html">Login</a>
            </div>
        <?php endif; ?>
    </div>
<!-- Cursos -->

</body>
</html>
