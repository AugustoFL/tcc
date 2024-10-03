
<?php 
session_start();
// Inicia a sessão para verificar o login   
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user = $logged_in ? $_SESSION['user'] : null;
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;

// Conexão com o banco de dados (usando MySQLi)
$host = 'localhost'; // Endereço do servidor
$dbname = 'escolamusica'; // Nome do banco de dados
$user_db = 'root'; // Usuário do banco de dados
$pass_db = ''; // Senha do banco de dados

$conn = new mysqli($host, $user_db, $pass_db, $dbname);

if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}

// Consulta SQL para buscar os cursos
$sql = "SELECT * FROM cursos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar e Cursos</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            height: 100%;
            overflow-x: hidden;
        }

        /* Ajuste da Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: rgb(0, 0, 0);
            position: fixed;
            width: 98%;
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
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .login-btn,
        .user-icon {
            padding: 10px 20px;
            background-color: #fafafa;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover,
        .user-icon:hover {
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
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
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

        /* Fundo animado */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #000000, #1f1e1e);
            overflow: hidden;
            z-index: -1;
            /* Coloca o fundo atrás de todo o conteúdo */
        }

        .note {
            position: absolute;
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.8);
            animation: float 10s infinite linear;
            opacity: 0.9;
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

        /* Ajustes dos cursos */
        .courses-container {
            padding: 80px 20px;
            text-align: center;
            position: relative;
            /* Mantém a posição relativa */
            z-index: 10;
            /* Certifica-se de que os cursos aparecem acima do fundo */
        }

        .courses {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .course-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 220px;
            transition: transform 0.3s ease;
            z-index: 10;
            /* Coloca os cards de curso acima do fundo */
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .course-image {
            width: 100%;
            border-radius: 8px;
            height: auto;
        }

        .enroll-btn {
            margin-top: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .enroll-btn:hover {
            background-color: #0056b3;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 200;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            transform: scale(0.7);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }

        .close-btn:hover {
            color: #f00;
        }

        .modal-content h3,
        .modal-content p {
            margin: 10px 0;
        }

        .modal-content .course-time,
        .modal-content .course-type {
            font-weight: bold;
            margin-top: 10px;
            color: #007BFF;
        }
        /* Estilo para o seletor */
select#tipo-inscricao {
    padding: 10px;
    font-size: 16px;
    border: 2px solid #007BFF;
    border-radius: 8px;
    background-color: #fafafa;
    color: #333;
    outline: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

select#tipo-inscricao:hover {
    border-color: #0056b3;
    background-color: #e6f2ff;
}

/* Estilo para o botão de confirmar inscrição */
button#confirm-enroll-btn {
    padding: 12px 20px;
    font-size: 18px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin-top: 15px;
}

button#confirm-enroll-btn:hover {
    background-color: #218838;
    transform: translateY(-3px);
}

button#confirm-enroll-btn:active {
    background-color: #1e7e34;
    transform: translateY(0);
}

    </style>
</head>

<body>
    <section id="home" class="section">
        <div class="background"></div> 
        <!-- Navbar -->
        <div class="navbar">
            <div class="logo"> <a href="#home" style="color: #fff; font-weight: bold; text-decoration: none;">Logo</a></div>
            <ul>
                <li><a href="#home">Início</a></li>
                <li><a href="#about">Sobre</a></li>
                <li><a href="#contact">Contato</a></li>
            </ul>
            <?php if ($logged_in): ?>
                <div class="user-menu"> <button class="user-icon">👤</button>
                    <div class="user-dropdown"> <a href="painelUsuario.php">Painel do Usuário</a> <a href="logout.php">Sair</a> </div>
                </div> 
            <?php else: ?>
                <div class="login-btn"> <a href="login.html">Login</a> </div>
            <?php endif; ?>
        </div>

        <!-- CURSOS -->
        <div class="courses-container">
            <h2 style="color:white">Nossos Cursos</h2>
            <div class="courses">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="course-card">
                            <img src="path/to/image.jpg" alt="Curso <?php echo $row['nome']; ?>" class="course-image">
                            <h3><?php echo $row['nome']; ?></h3>
                            <p><?php echo $row['descricao']; ?></p>
                            <button class="enroll-btn" data-course-id="<?php echo $row['id']; ?>"
                                data-course-image="path/to/image.jpg"
                                data-course-desc="<?php echo $row['descricao']; ?>"
                                data-course-time="Horário: <?php echo $row['horario']; ?>"
                                data-course-type="<?php echo $row['tipo']; ?>">Inscrever-se</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nenhum curso disponível.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<!-- Modal -->
<div class="modal" id="course-modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <img id="modal-image" src="" alt="Imagem do Curso">
        <h3 id="modal-title">Título do Curso</h3>
        <p id="modal-desc">Descrição do curso.</p>
        <p id="modal-time" class="course-time">Horário do curso</p>

        <!-- Select para escolher o tipo de inscrição -->
        <label for="tipo-inscricao">Escolha o tipo de inscrição:</label>
        <select id="tipo-inscricao">
            <option value="Individual">Individual</option>
            <option value="Grupo">Grupo</option>
        </select>

        <!-- Botão para confirmar a inscrição -->
        <button id="confirm-enroll-btn">Confirmar Inscrição</button>
    </div>
</div>

<script>

    const notes = ['♪', '♫', '♬', '♩', '♭'];
    const guitars = ['🎸'];
    const background = document.querySelector('.background');
    let currentIcons = notes;

    // Função que cria um ícone animado no fundo
    function createNote() {
        const note = document.createElement('div');
        note.classList.add('note');
        note.innerHTML = currentIcons[Math.floor(Math.random() * currentIcons.length)];
        note.style.left = `${Math.random() * 100}vw`;  // Corrigido: uso de template literal
        note.style.animationDuration = `${Math.random() * 5 + 5}s`;  // Corrigido: uso de template literal
        background.appendChild(note);

        // Remove a nota após 10 segundos
        setTimeout(() => {
            note.remove();
        }, 10000);
    }

    // Inicia a animação de notas no fundo
    setInterval(createNote, 75);

    // Verifica se o elemento com a classe '.curso-violao' existe antes de adicionar eventos
    document.addEventListener('DOMContentLoaded', () => {
        const violaoCourse = document.querySelector('.curso-violao');
        if (violaoCourse) {
            violaoCourse.addEventListener('mouseover', function () {
                currentIcons = guitars;
            });

            violaoCourse.addEventListener('mouseout', function () {
                currentIcons = notes;
            });
        }
    });
</script>

    </script>

    <script>
       // Funções JavaScript para exibir o modal e manipular os cursos
const modal = document.getElementById('course-modal');
const modalImage = document.getElementById('modal-image');
const modalTitle = document.getElementById('modal-title');
const modalDesc = document.getElementById('modal-desc');
const modalTime = document.getElementById('modal-time');
const tipoInscricaoSelect = document.getElementById('tipo-inscricao');
const closeModalBtn = document.querySelector('.close-btn');
const confirmEnrollBtn = document.getElementById('confirm-enroll-btn');

let selectedCourseId = null;

function openModal(courseId, image, desc, time) {
    selectedCourseId = courseId;
    modalImage.src = image;
    modalTitle.textContent = 'Curso ' + courseId;
    modalDesc.textContent = desc;
    modalTime.textContent = time;
    modal.classList.add('show');
}

// Fecha o modal
closeModalBtn.addEventListener('click', function () {
    modal.classList.remove('show');
    setTimeout(() => { modal.style.display = 'none'; }, 500);
});

// Fecha o modal ao clicar fora do conteúdo
window.addEventListener('click', function (e) {
    if (e.target === modal) {
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 500);
    }
});

// Evento de clique no botão "Inscrever-se"
const enrollButtons = document.querySelectorAll('.enroll-btn');
enrollButtons.forEach(button => {
    button.addEventListener('click', function () {
        const courseId = this.getAttribute('data-course-id');
        const image = this.getAttribute('data-course-image');
        const desc = this.getAttribute('data-course-desc');
        const time = this.getAttribute('data-course-time');
        openModal(courseId, image, desc, time);
        modal.style.display = 'flex';
    });
});

// Enviar a inscrição quando o usuário confirmar
confirmEnrollBtn.addEventListener('click', function () {
    const tipoInscricao = tipoInscricaoSelect.value;

    fetch('inscrever.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `course_id=${selectedCourseId}&tipo_inscricao=${tipoInscricao}`
    })
    .then(response => response.text())
    .then(data => {
        alert(data);  // Exibe uma mensagem de sucesso ou erro
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 500);
    });
});
    </script>
</body>

</html>
