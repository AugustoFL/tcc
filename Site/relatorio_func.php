<?php 
session_start();


// Verifica o tipo de usuário
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';

// Apenas funcionários podem acessar a página de relatórios
if ($tipo_usuario !== 'funcionario') {
    header("Location: index2.php"); // Redireciona para o painel principal se não for funcionário
    exit;
}

// Conexão com o banco de dados
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "escolamusica"; 
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Variáveis de filtro
$cpfFiltro = isset($_GET['cpf']) ? $_GET['cpf'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Número de resultados por página
$resultados_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $resultados_por_pagina;

// Query base para contar os registros
$sql_count = "SELECT COUNT(*) as total FROM relatorios WHERE 1=1";

// Condições da query (filtros por CPF e datas)
$condicoes = [];
$parametros = [];
$tipos_parametros = '';

// Filtro por CPF
if (!empty($cpfFiltro)) {
    $condicoes[] = "usuario_cpf = ?";
    $parametros[] = $cpfFiltro;
    $tipos_parametros .= 's';
}

// Filtro por intervalo de datas
if (!empty($data_inicio) && !empty($data_fim)) {
    $condicoes[] = "data BETWEEN ? AND ?";
    $parametros[] = $data_inicio;
    $parametros[] = $data_fim;
    $tipos_parametros .= 'ss';
}

// Adiciona as condições à query
if (count($condicoes) > 0) {
    $sql_count .= " AND " . implode(" AND ", $condicoes);
}

// Preparar a consulta de contagem
$stmt_count = $conn->prepare($sql_count);

// Associa parâmetros, se houver filtros
if (!empty($tipos_parametros)) {
    $stmt_count->bind_param($tipos_parametros, ...$parametros);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $resultados_por_pagina);

// Query para obter os dados paginados
$sql = "SELECT id, DATE_FORMAT(data, '%d/%m/%Y') as data_formatada, descricao, usuario_cpf FROM relatorios WHERE 1=1";

// Adiciona as mesmas condições de filtros para a query de dados
if (count($condicoes) > 0) {
    $sql .= " AND " . implode(" AND ", $condicoes);
}
$sql .= " LIMIT ?, ?";

// Preparar a consulta para os dados paginados
$stmt = $conn->prepare($sql);

// Adicionar os parâmetros de filtro e os limites de paginação
$parametros[] = $offset;
$parametros[] = $resultados_por_pagina;
$tipos_parametros .= 'ii';
$stmt->bind_param($tipos_parametros, ...$parametros);

$stmt->execute();
$result = $stmt->get_result();
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            color: #f1f1f1;
            background-color: #444;
            padding: 10px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: #750a67;
        }
        .pagination a.active {
            background-color: #750a67;
        }
    </style>
</head>
<body>
    <!-- Menu lateral -->
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
        <?php endif; ?>

        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div class="main-content">
        <h1>Relatórios</h1>

        <!-- Formulário de Filtros -->
        <form class="filter-form" method="GET" action="">
            <label for="cpf">CPF:</label>
            <input type="text" name="cpf" id="cpf" placeholder="Digite o CPF" value="<?php echo htmlspecialchars($cpfFiltro); ?>">
            
            <label for="data_inicio">Data Início:</label>
            <input type="date" name="data_inicio" id="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>">
            
            <label for="data_fim">Data Fim:</label>
            <input type="date" name="data_fim" id="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>">

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

            <!-- Controle de paginação -->
            <div class="pagination">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?pagina=<?php echo $pagina_atual - 1; ?>&cpf=<?php echo htmlspecialchars($cpfFiltro); ?>&data_inicio=<?php echo htmlspecialchars($data_inicio); ?>&data_fim=<?php echo htmlspecialchars($data_fim); ?>">&laquo; Página Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?php echo $i; ?>&cpf=<?php echo htmlspecialchars($cpfFiltro); ?>&data_inicio=<?php echo htmlspecialchars($data_inicio); ?>&data_fim=<?php echo htmlspecialchars($data_fim); ?>" <?php if ($i == $pagina_atual) echo 'class="active"'; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?pagina=<?php echo $pagina_atual + 1; ?>&cpf=<?php echo htmlspecialchars($cpfFiltro); ?>&data_inicio=<?php echo htmlspecialchars($data_inicio); ?>&data_fim=<?php echo htmlspecialchars($data_fim); ?>">Próxima Página &raquo;</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Nenhum relatório encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>

