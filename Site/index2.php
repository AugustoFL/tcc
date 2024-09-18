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
            background: #f9f9f9;
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
        .login-btn:hover, .user-icon:hover: a {
            
            color: #fafafa;
            
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

        .containerSobre {
            display: flex;
            justify-content: space-around;
            padding: 20px;
}           

.columnSobre {    
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 30%;
}

.contact-info {
    font-size: 17px;
    margin-top: 20px;
    text-align: center;
}

.contact-info p {
    font-size: 17px;
    margin: 10px 0;
    color: #000; /* Cor do texto de contato */
}

.contact-info a {
    font-size: 17px;
    color: #000;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: underline;
    
}
#contact h2 {
    color: #000;
    font-size: 25px;
}

  /* Estilo da vitrine de cursos */
  .vitrine {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin: 50px auto;
        }

        .course {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .course:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .course img {
            width: 100%;
            height: auto;
        }

        .course-details {
            padding: 15px;
            text-align: center;
        }

        .course-title {
            font-size: 1.2em;
            margin: 0 0 10px;
        }

        .course-price {
            color: #b12704;
            font-size: 1.1em;
            margin: 0 0 10px;
        }

        .course-description {
            font-size: 0.9em;
            color: #555;
        }

        .course button {
            background-color: #750a67;
            colorwhite;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .course button:hover {
            background-color: #a0128b;
        }

        .course button:active {
            background-color: #520439;
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
                   <!-- <//?php if ($tipo_usuario === 'funcionario'): ?>
                        <a href="#">Painel do Funcionário</a>
                    <//?php endif; ?> -->
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
    <!-- Seção de Cursos -->
    <section id="courses" class="section" style="background-color: #fff;">
        <h1 style="color: #000;">Nossos Cursos</h1>
        <div class="vitrine">
            <div class="course">
                <img src="https://via.placeholder.com/300" alt="Curso de Violão">
                <div class="course-details">
                    <h2 class="course-title">Curso de Violão</h2>
                    <p class="course-price">Gratuito</p>
                    <p class="course-description">Aprenda a tocar violão com aulas práticas e teóricas. Curso para iniciantes e avançados.</p>
                    <button>Inscrever-se</button>
                </div>
            </div>
            <div class="course">
                <img src="https://via.placeholder.com/300" alt="Curso de Canto">
                <div class="course-details">
                    <h2 class="course-title">Curso de Canto</h2>
                    <p class="course-price">Gratuito</p>
                    <p class="course-description">Desenvolva suas habilidades vocais com nosso curso completo de canto.</p>
                    <button>Inscrever-se</button>
                </div>
            </div>
            <div class="course">
                <img src="https://via.placeholder.com/300" alt="Curso de Teclado">
                <div class="course-details">
                    <h2 class="course-title">Curso de Teclado</h2>
                    <p class="course-price">Gratuito</p>
                    <p class="course-description">Domine o teclado em nosso curso dinâmico para todos os níveis.</p>
                    <button>Inscrever-se</button>
                </div>
            </div>
            </div>
        </div>
    </section>
    <!-- Seção Sobre Nós -->
    <section id="about" class="section">
      <h1>Sobre nós</h1> 
      <div class="containerSobre">       
        <div class="columnSobre">
            <h2>Quem somos?</h2>
            <p>A escola municipal de música de Ourinhos é uma instituição com o intuito de promover a cultura na cidade, oferecendo cursos gratuitos em diversas modalidades da música, com instrumentos e até aulas de canto.</p>
        </div>
        <div class="columnSobre">
            <h2>Como funciona?</h2>
            <p>A instituição oferece cursos coletivos e individuais para os alunos que se interessarem, seguindo uma lista de espera. Você pode conferir os cursos e como funcionamos na página do curso.</p>
        </div>
        <div class="columnSobre">
            <h2>Contato</h2>
            <p>Para mais informações, entre em contato conosco através do nosso site na seção 'Contato' ou visite nossa sede em Ourinhos.</p>
        </div>
    </div>
    </section>

    <section id="contact" class="section">
        <h2>Contato</h2>
        <p>Entre em contato conosco.</p>
        <div class="contact-info">
            <p><strong>Endereço:</strong> Rua Treze de Maio 300 (Vila Perino), Ourinhos</p>
            <p><strong>Telefone:</strong> (14) 3302-1815</p>
            <p><strong>Email:</strong> <a href="mailto:escolademusicadeourinhos2020@gmail.com">escolademusicadeourinhos2020@gmail.com</a></p>
        </div>
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



