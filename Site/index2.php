<?php
session_start(); // Inicia a sessão para verificar o login

// Verifica se o usuário está logado
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user = $logged_in ? $_SESSION['user'] : null;
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null; // Define como cliente se não estiver logado
// Verifique se o tipo de usuário está correto
var_dump($_SESSION['tipo_usuario']); // Verifica o valor de tipo_usuario

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style>
        /* Estilo existente */
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
            width: 95.8%;
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

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #000000, #1f1e1e);
            overflow: hidden;
            z-index: -1;
        }

        .note {
            position: absolute;
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.8);
            opacity: 0.9;
            animation: float 10s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(50px);
                opacity: 0;
            }
        }

        .section {
            padding: 100px 20px;
            color: #ffffff;
            text-align: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: relative;
        }

        #home {
            background: rgba(0, 0, 0, 0.7);
            position: relative;
        }

        #about {
            background: rgba(255, 215, 0, 0.7);
            color: #000;
        }

        #contact {
            background: rgba(70, 130, 180, 0.7);
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
            color: white;
        }

        /* User icon dropdown */
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

        /* Preloader */
        #preloader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: hidden;
            opacity: 1;
            transition: opacity 1s ease;
        }

        .preloader-notes {
            position: relative;
            width: 100%;
            height: 80px;
        }

        .preloader-notes .note {
            position: absolute;
            font-size: 2em;
            color: #ffffff;
            animation: preloader-float 3s linear infinite;
            opacity: 0;
        }

        @keyframes preloader-float {
            0% {
                transform: translateY(100%);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        .preloader-notes .note:nth-child(1) {
            left: 45%;
            animation-delay: 0s;
        }

        .preloader-notes .note:nth-child(2) {
            left: 50%;
            animation-delay: 0.5s;
        }

        .preloader-notes .note:nth-child(3) {
            left: 55%;
            animation-delay: 1s;
        }

        .preloader-notes .note:nth-child(4) {
            left: 60%;
            animation-delay: 1.5s;
        }

        #preloader p {
            margin-top: 20px;
            font-size: 1.2em;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Preloader temático -->
    <div id="preloader">
        <div class="preloader-notes">
            <div class="note">♪</div>
            <div class="note">♫</div>
            <div class="note">♩</div>
        </div>
        <p>Carregando...</p>
    </div>

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
                    <?php if ($tipo_usuario === 'funcionario'): ?>
                        <a href="#">Painel do Funcionário</a>
                    <?php endif; ?>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login-btn">
                <a href="login.html">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Seção com fundo animado -->
    <section id="home" class="section">
        <div class="background"></div>
        <h1>Bem-vindo à Landing Page</h1>
        <p>Explore o conteúdo abaixo.</p>
    </section>

    <!-- Outras Seções -->
    <section id="about" class="section">
        <h2>Sobre Nós</h2>
        <p>Informações sobre a empresa.</p>
    <?php // Verifique se o tipo de usuário está correto
var_dump($_SESSION['tipo_usuario']);  ?>
    </section>

    <section id="contact" class="section">
        <h2>Contato</h2>
        <p>Entre em contato conosco.</p>
    </section>

    <script>
        const notes = ['♪', '♫', '♬', '♩', '♭'];
        const background = document.querySelector('.background');

        function createNote() {
            const note = document.createElement('div');
            note.classList.add('note');
            note.innerHTML = notes[Math.floor(Math.random() * notes.length)];
            note.style.left = `${Math.random() * 100}vw`;
            note.style.animationDuration = `${Math.random() * 5 + 5}s`;
            background.appendChild(note);

            setTimeout(() => {
                note.remove();
            }, 10000);
        }

        setInterval(createNote, 75);

        // Mantém o preloader visível por 3 segundos após o carregamento da página
        window.addEventListener('load', () => {
            setTimeout(() => {
                const preloader = document.getElementById('preloader');
                preloader.style.opacity = '0'; // Fade-out effect
                setTimeout(() => {
                    preloader.style.display = 'none'; // Remove preloader after fade-out
                }, 1000);
            }, 2000);
        });
    </script>

</body>
</html>



