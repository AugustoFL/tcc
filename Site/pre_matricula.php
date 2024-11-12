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

        ul {

            list-style-type: none;

            padding: 0;

        }

 

        ul li {

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

        
    </style>

</head>

<body>

 

<div class="form-container">

    <h1>Pré-Matrícula da Escola de Música de Ourinhos</h1>

            <!-- Exibe o curso e o tipo selecionado -->

    <div class="course-info" id="course-info">

        <?php

        // Conectar ao banco de dados

        $servername = "localhost"; // Substitua pelo seu servidor

        $username = "root"; // Substitua pelo seu usuário

        $password = ""; // Substitua pela sua senha

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

 

    <!-- Primeira Página -->

    <div class="form-page active" id="page1">

        <h2>Informações Importantes</h2>

        <p>Este formulário foi criado para tornar mais simples o processo de inscrição na Escola de Música de Ourinhos.</p>

        <ul>

            <li>Preenchimento do Formulário</li>

            <li>Confirmação via WhatsApp</li>

            <li>Verificação de Vagas</li>

            <li>Lista de Espera e Confirmação de Matrícula</li>

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

 

        <label for="cpf">CPF (Apenas números, obrigatório)</label>

        <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF" maxlength="14" required>

 

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
        <label>Qual o Período Disponível para as Aulas? *</label>
        <div class="label-container">
            <input type="checkbox" id="manha" name="periodo_aulas" value="Manhã">
            <label for="manha">Manhã (7h-12h)</label>
        </div>
        <div class="label-container">
            <input type="checkbox" id="tarde" name="periodo_aulas" value="Tarde">
            <label for="tarde">Tarde (13h-18h)</label>
        </div>
        <div class="label-container">
            <input type="checkbox" id="noite" name="periodo_aulas" value="Noite">
            <label for="noite">Noite (18h-22h)</label>
        </div>
        
        <h2>Termo de Autorização de Uso de Imagem</h2>
        <div class="label-container">
            <input type="checkbox" id="uso-imagem" name="uso-imagem" required>
            <label for="uso-imagem">Pelo presente e até expressa revogação por escrito CONCEDO a Secretaria de Cultura, o direito de utilizar gratuitamente o som de minha voz e a minha imagem, conforme artigo 5º, X, CF e artigos 20º, 22º, 24º, 25º, 49º, a 56º da Lei 5250-67 – Lei de Imprensa e demais dispositivos em vigor. Informo ainda que tudo que declarei foi espontâneo, sem interferência e sobretudo representa a verdade.</label>
        </div>
        
        <button type="button" onclick="prevPage()">Anterior</button>
        <button type="button" onclick="submitForm()">Enviar</button>
    </div>
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

 

</body>

</html>