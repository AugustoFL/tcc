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

    <title>Pré-Matrícula - Escola de Música de Ourinhos</title>

    <style>

        /* Estilo base do corpo */

        body {

            font-family: 'Arial', sans-serif;

            background-color: #121212;

            color: #e0e0e0;

            margin: 0;

            padding: 0;

        }

        h1, h2 {

            text-align: center;

            color: #ffffff;

        }

        h1 {

            margin-bottom: 5px;

            font-size: 2rem;

        }

        h2 {

            margin-bottom: 15px;

            font-size: 1.5rem;

        }

        /* Exibir o nome do curso selecionado */

        .course-info {

            text-align: center;

            color:  #ffff;

            font-size: 1.2rem;

            margin-bottom: 20px;

        }

        /* Contêiner do formulário */

        .form-container {

            max-width: 600px;

            margin: 40px auto;

            padding: 30px;

            background-color: #1e1e1e;

            border-radius: 10px;

            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);

        }

        label {

            display: block;

            font-weight: bold;

            margin-bottom: 5px;

            color: #e0e0e0;

        }

        input, select, textarea {

            width: 95%;

            padding: 12px;

            margin-bottom: 20px;

            background-color: #333;

            color: #e0e0e0;

            border: 2px solid #555;

            border-radius: 5px;

            transition: all 0.3s ease;

            font-size: 1rem;

        }

        input:focus, select:focus, textarea:focus {

            border-color: #8B008B;

            box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);

            outline: none;

        }

        /* Botões de navegação */

        button {

            padding: 12px 25px;

            background-color: #592693;

            color: white;

            border: none;

            border-radius: 8px;

            cursor: pointer;

            font-size: 1rem;

            transition: background-color 0.3s ease, transform 0.2s ease;

        }

        button:hover {

            background-color: #8B008B;

        }

        button:active {

            transform: scale(0.98);

        }

        /* Ocultar páginas do formulário por padrão */

        .form-page {

            display: none;

            opacity: 0;

            transform: translateX(-100%);

            transition: opacity 0.5s ease, transform 0.5s ease;

        }

        /* Página ativa */

        .form-page.active {

            display: block;

            opacity: 1;

            transform: translateX(0);

        }

        /* Estilização da lista na primeira página */

        ul.form {

            list-style-type: none;

            padding: 0;

        }

        ul.form li.form1 {

            padding: 10px;

            background-color: #2e2e2e;

            margin-bottom: 8px;

            border-left: 4px solid  #8B008B;

            color: #e0e0e0;

        }

        /* Estilo de erro */

        .error {

            color: #ff6347;

            margin-top: -15px;

            margin-bottom: 15px;

            font-size: 0.9rem;

        }

        /* Animação suave de transição entre páginas */

        .slide-in-left {

            animation: slideInLeft 0.5s forwards;

        }

        .slide-in-right {

            animation: slideInRight 0.5s forwards;

        }

        @keyframes slideInLeft {

            0% {

                transform: translateX(-100%);

                opacity: 0;

            }

            100% {

                transform: translateX(0);

                opacity: 1;

            }

        }

        @keyframes slideInRight {

            0% {

                transform: translateX(100%);

                opacity: 0;

            }

            100% {

                transform: translateX(0);

                opacity: 1;

            }

        }

/* Estilo e animação do checkbox */

.label-container {

            display: flex;

            align-items: center;

            margin: 10px 0;

        }

        .label-container input[type="checkbox"] {

            appearance: none;

            width: 25px;

            height: 25px;

            background-color: #333;

            border: 2px solid #8B008B;

            border-radius: 5px;

            margin-right: 10px;

            position: relative;

            cursor: pointer;

            transition: all 0.3s ease;

        }

        .label-container input[type="checkbox"]:checked {

            background-color: #8B008B;

            transform: scale(1.1);

        }

        /* Adicionando uma marca de seleção animada */

        .label-container input[type="checkbox"]::after {

            content: '';

            position: absolute;

            top: 50%;

            left: 50%;

            width: 10px;

            height: 10px;

            background-color: white;

            border-radius: 2px;

            opacity: 0;

            transform: translate(-50%, -50%) scale(0);

            transition: all 0.2s ease;

        }

        .label-container input[type="checkbox"]:checked::after {

            opacity: 1;

            transform: translate(-50%, -50%) scale(1);

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

        .navbar ul.nav1 {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul.nav1 li.nav {
            margin-left: 20px;
        }

        .navbar ul.nav1 li.nav a {
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

    </style>

</head>

<body>
            <!-- Navbar -->
            <div class="navbar">
        <div class="logo">
            <a href="#home" style="color: #fff; font-weight: bold; text-decoration: none;">Logo</a>
        </div>
        <ul class="nav1">
            <li class="nav"><a href="">Início</a></li>
            <li class="nav"><a href="">Sobre</a></li>
            <li class="nav"><a href="">Contato</a></li>
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

<div class="form-container">

    <h1>Pré-Matrícula da Escola de Música de Ourinhos</h1>

            <!-- Exibe o curso e o tipo selecionado -->

    <div class="course-info" id="course-info">

        <?php

        // Conectar ao banco de dados

        $servername = "localhost"; 

        $username = "root"; 

        $password = ""; 

        $dbname = "escolamusica";

        // Criar a conexão

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexão

        if ($conn->connect_error) {

            die("Falha na conexão: " . $conn->connect_error);

        }

        // Obter os parâmetros da URL
        $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;
        $tipo_inscricao = isset($_GET['tipo_inscricao']) ? $_GET['tipo_inscricao'] : null;
        $cpf = $logged_in ? $_SESSION['user'] : null;
        // Buscar o nome do curso baseado no course_id

        if ($course_id) {

            $sql = "SELECT nome FROM cursos WHERE id = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param("i", $course_id);

            $stmt->execute();

            $stmt->bind_result($nome_curso);

            $stmt->fetch();

            $stmt->close();

            // Verificar se o nome do curso foi encontrado

            if ($nome_curso && $tipo_inscricao) {

                echo htmlspecialchars($nome_curso) . " - " . htmlspecialchars($tipo_inscricao);

            } else {

                echo "Curso ou tipo de inscrição não informado.";

            }

        } else {

            echo "Curso ou tipo de inscrição não informado.";

        }

        // Fechar a conexão

        $conn->close();

        ?>

    </div>

    <form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">

    <input type="hidden" name="tipo_inscricao" value="<?php echo htmlspecialchars($tipo_inscricao); ?>">

    <!-- Primeira Página -->

    <div class="form-page active" id="page1">

        <h2>Informações Importantes</h2>

        <p>Este formulário foi criado para tornar mais simples o processo de inscrição na Escola de Música de Ourinhos.</p>

        <ul class="form">

            <li class="form1">Preenchimento do Formulário</li>

            <li class="form1">Confirmação via WhatsApp</li>

            <li class="form1">Verificação de Vagas</li>

            <li class="form1">Lista de Espera e Confirmação de Matrícula</li>

        </ul>

        <label for="email">E-mail (Obrigatório)</label>

        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

        <button type="button" onclick="nextPage()">Próximo</button>

        <span class="error" id="emailError"></span>

    </div>

    <!-- Segunda Página -->

    <div class="form-page" id="page2">

        <h2>Dados Pessoais</h2>

        <label for="nome">Nome Completo (Obrigatório)</label>

        <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" maxlength="100" required>

        <?php
        function formatCpf($cpf) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
        }
        ?>

        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars(formatCpf($cpf)); ?>" readonly>

        <label for="rg">RG (Obrigatório)</label>

        <input type="text" id="rg" name="rg" placeholder="Digite seu RG" maxlength="12" required>

        <label for="data-nascimento">Data de Nascimento (Obrigatório)</label>

        <input type="text" id="data-nascimento" name="data-nascimento" placeholder="DD/MM/AAAA" maxlength="10" required>

        <label for="doc-identificacao">Anexar Documento de Identificação com Foto (PDF, DOC, JPG, máximo 10MB, obrigatório)</label>

        <input type="file" id="doc-identificacao" name="doc-identificacao" accept=".pdf,.doc,.jpg,.jpeg,.png" required>

        <label for="telefone">Telefone (WhatsApp) (Apenas números, obrigatório)</label>

        <input type="text" id="telefone" name="telefone" placeholder="Digite seu telefone no formato 14999995555" maxlength="15" required>

        <label for="endereco">Endereço Completo (Rua, Número, Bairro, CEP, obrigatório)</label>

        <input type="text" id="endereco" name="endereco" placeholder="Digite seu endereço completo" required>

        <label for="comprovante-endereco">Anexar Comprovante de Endereço (PDF, DOC, JPG, máximo 10MB, obrigatório)</label>

        <input type="file" id="comprovante-endereco" name="comprovante-endereco" accept=".pdf,.doc,.jpg,.jpeg,.png" required>

        <label for="nome-responsavel">Nome Completo do Responsável (Opcional para menores de 18 anos)</label>

        <input type="text" id="nome-responsavel" name="nome-responsavel" placeholder="Digite o nome do responsável" maxlength="100">

        <button type="button" onclick="prevPage()">Anterior</button>

        <button type="button" onclick="nextPage()">Próximo</button>

    </div>

    <div class="form-page" id="page3">

        <h2>Período Disponível para as Aulas</h2>

        <label>Qual o Período Disponível para as Aulas? (Obrigatório)</label>

        <div class="label-container">

            <input type="checkbox" id="manha" name="periodo_aulas[]" value="Manhã">

            <label for="manha">Manhã (7h-12h)</label>

        </div>

        <div class="label-container">

            <input type="checkbox" id="tarde" name="periodo_aulas[]" value="Tarde">

            <label for="tarde">Tarde (13h-18h)</label>

        </div>

        <div class="label-container">

            <input type="checkbox" id="noite" name="periodo_aulas[]" value="Noite">

            <label for="noite">Noite (18h-22h)</label>

        </div>

        <h2>Termo de Autorização de Uso de Imagem</h2>

        <div class="label-container">

            <input type="checkbox" id="uso-imagem" name="uso-imagem" required>

            <label for="uso-imagem">Pelo presente e até expressa revogação por escrito CONCEDO a Secretaria de Cultura, o direito de utilizar gratuitamente o som de minha voz e a minha imagem, conforme artigo 5º, X, CF e artigos 20º, 22º, 24º, 25º, 49º, a 56º da Lei 5250-67 – Lei de Imprensa e demais dispositivos em vigor. Informo ainda que tudo que declarei foi espontâneo, sem interferência e sobretudo representa a verdade.</label>

        </div>

        <button type="button" onclick="prevPage()">Anterior</button>

        <button type="submit">Enviar</button>

    </div>

    </form>

</div>

            <script>
        // Aplica máscara ao CPF no formato "XXX.XXX.XXX-XX"
        document.getElementById('cpf').addEventListener('input', function (e) {

            e.target.value = e.target.value

                .replace(/\D/g, '') // Remove tudo que não é dígito

                .replace(/(\d{3})(\d)/, '$1.$2') // Adiciona ponto entre o terceiro e quarto dígitos

                .replace(/(\d{3})(\d)/, '$1.$2') // Adiciona outro ponto entre o sexto e sétimo dígitos

                .replace(/(\d{3})(\d{1,2})$/, '$1-$2'); // Adiciona hífen entre o nono e décimo dígitos

        });

        // Aplica máscara ao RG no formato "XX.XXX.XXX-X"

        document.getElementById('rg').addEventListener('input', function (e) {

            e.target.value = e.target.value

                .replace(/\D/g, '') // Remove tudo que não é dígito

                .replace(/(\d{2})(\d)/, '$1.$2') // Adiciona ponto entre o segundo e terceiro dígitos

                .replace(/(\d{3})(\d)/, '$1.$2') // Adiciona ponto entre o quinto e sexto dígitos

                .replace(/(\d{3})(\d{1})$/, '$1-$2'); // Adiciona hífen no final

        });

        // Aplica máscara para telefone no formato "(XX) XXXXX-XXXX"

        document.getElementById('telefone').addEventListener('input', function (e) {

            e.target.value = e.target.value

                .replace(/\D/g, '') // Remove tudo que não é dígito

                .replace(/(\d{2})(\d)/, '($1) $2') // Adiciona parênteses nos dois primeiros dígitos

                .replace(/(\d{5})(\d)/, '$1-$2') // Adiciona hífen após o quinto dígito

                .replace(/(-\d{4})\d+?$/, '$1'); // Limita em nove dígitos no total

        });

        // Aplica máscara para data de nascimento no formato "DD/MM/AAAA"

        document.getElementById('data-nascimento').addEventListener('input', function (e) {

            e.target.value = e.target.value

                .replace(/\D/g, '') // Remove tudo que não é dígito

                .replace(/(\d{2})(\d)/, '$1/$2') // Adiciona barra após o dia

                .replace(/(\d{2})(\d)/, '$1/$2') // Adiciona barra após o mês

                .replace(/(\d{4})\d+?$/, '$1'); // Limita em oito dígitos (DD/MM/AAAA)

        });

    </script>

<script>

    let currentPage = 1;

    const totalPages = 3; // Número de páginas implementadas

    function showPage(page, direction) {

        document.querySelectorAll('.form-page').forEach((pageElement) => {

            pageElement.classList.remove('active', 'slide-in-left', 'slide-in-right');

        });

        const animationClass = direction === 'next' ? 'slide-in-right' : 'slide-in-left';

        const currentPageElement = document.getElementById('page' + page);

        currentPageElement.classList.add('active', animationClass);

    }

    function nextPage() {

        if (currentPage === 1) {

            const email = document.getElementById('email').value;

            if (!email) {

                document.getElementById('emailError').textContent = 'Por favor, insira seu e-mail.';

                return;

            } else {

                document.getElementById('emailError').textContent = '';

            }

        } else if (currentPage === 2) {

            const nome = document.getElementById('nome').value;

            const cpf = document.getElementById('cpf').value;

            const rg = document.getElementById('rg').value;

            const dataNascimento = document.getElementById('data-nascimento').value;

            const docIdentificacao = document.getElementById('doc-identificacao').value;

            const telefone = document.getElementById('telefone').value;

            const endereco = document.getElementById('endereco').value;

            const comprovanteEndereco = document.getElementById('comprovante-endereco').value;

            if (!nome || !cpf || !rg || !dataNascimento || !docIdentificacao || !telefone || !endereco || !comprovanteEndereco) {

                alert('Por favor, preencha todos os campos obrigatórios.');

                return;

            }

        }

        if (currentPage < totalPages) {

            currentPage++;

            showPage(currentPage, 'next');

        }

    }

    function prevPage() {

        if (currentPage > 1) {

            currentPage--;

            showPage(currentPage, 'prev');

        }

    }

    showPage(currentPage, 'next');

</script>

<?php
// Configuração de conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escolamusica";

// Estabelece a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura dos dados enviados
    $email = $_POST['email'];
    $nome = $_POST['nome'];
    $cpf = $logged_in ? $_SESSION['user'] : null;
    $rg = $_POST['rg'];

    // Converte a data de DD/MM/AAAA para o formato YYYY-MM-DD
    $data_nascimento = $_POST['data-nascimento'];
    $data_nascimento = DateTime::createFromFormat('d/m/Y', $data_nascimento)->format('Y-m-d');

    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    // Concatena os valores dos checkboxes
    $periodo_aulas = isset($_POST['periodo_aulas']) 
        ? implode(", ", $_POST['periodo_aulas']) 
        : '';

    $course_id = $_POST['course_id'];
    $tipo_curso = $_POST['tipo_inscricao']; // Agora mapeado diretamente para `tipo_curso`

    // Defina o status inicial como 'Formulario'
    $status = 'Formulario';

    // SQL para inserir dados na tabela `matricula`
    $sql = "INSERT INTO matricula (nome, email, telefone, cpf, rg, data_nascimento, endereco, periodo_aulas, course_id, tipo_curso, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar e executar a inserção
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $nome, $email, $telefone, $cpf, $rg, $data_nascimento, $endereco, $periodo_aulas, $course_id, $tipo_curso, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Dados enviados com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao enviar os dados: " . $stmt->error . "');</script>";
    }

    // Fecha o statement
    $stmt->close();
}

// Fecha a conexão apenas após a inserção ser concluída
$conn->close();
?>
</body>
</html>