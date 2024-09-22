<?php 
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
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

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "escolamusica"; 

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Variável de filtro
$cpfFiltro = isset($_GET['cpf']) ? $_GET['cpf'] : '';

// Query base
$sql = "SELECT id, DATE_FORMAT(data, '%d/%m/%Y') as data_formatada, descricao, usuario_cpf FROM relatorios WHERE 1=1";

// Adiciona filtro na query se preenchido
if (!empty($cpfFiltro)) {
    $sql .= " AND usuario_cpf = ?";
}

// Preparar a consulta
$stmt = $conn->prepare($sql);

// Verifica se o CPF foi preenchido e o associa à consulta
if (!empty($cpfFiltro)) {
    $stmt->bind_param("s", $cpfFiltro);
}

$stmt->execute();
$result = $stmt->get_result();

// Feche a conexão após obter os resultados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios</title>
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
        .main-content {
            margin-left: 300px;
            padding: 40px;
            background-color: #2b2b2b;
            flex: 1;
            overflow-y: auto;
            height: 100%;
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
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #333;
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
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form input {
            padding: 8px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
        }
        .filter-form button {
            padding: 8px 12px;
            background-color: #750a67;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #a00;
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
            <a href="#">Gerenciar Usuários</a>
            <a href="relatorio_func.php">Relatórios</a>
        <?php endif; ?>

        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div class="main-content">
        <h1>Relatórios</h1>

        <!-- Formulário de Filtros -->
        <form class="filter-form" method="GET" action="">
            <label for="cpf">CPF:</label>
            <input type="text" name="cpf" id="cpf" placeholder="Digite o CPF" value="<?php echo htmlspecialchars($cpfFiltro); ?>">
            
            <button type="submit">Filtrar</button>
        </form>

        <!-- Tabela de Relatórios -->
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CPF</th>
                        <th>Data</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['usuario_cpf']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_formatada']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum relatório encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
