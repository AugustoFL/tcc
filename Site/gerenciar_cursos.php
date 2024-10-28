<?php
session_start();
require_once 'conexao.php'; // Arquivo de conexão com o banco de dados

// Função para verificar se o usuário é funcionário autenticado
function verificarAcesso() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.html");
        exit;
    }
}

// Verifica acesso do usuário
verificarAcesso();

// Define o tipo de usuário
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';
if ($tipo_usuario !== 'funcionario') {
    header("Location: index2.php");
    exit;
}

// Configurações de paginação
$cursosPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $cursosPorPagina;

// Consulta para contar o total de cursos
$totalCursosQuery = "SELECT COUNT(*) as total FROM cursos";
$totalCursosResult = $conn->query($totalCursosQuery);
$totalCursos = $totalCursosResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalCursos / $cursosPorPagina);

// Consulta para buscar os cursos com limite e offset
$query = "SELECT id, nome, descricao, horario, tipo FROM cursos LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $cursosPorPagina, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cursos</title>
    <style>
       /* Estilo geral do corpo */
body {
    font-family: Arial, sans-serif;
    background-color: #1f1f1f;
    color: #f1f1f1;
    margin: 0;
    padding: 0;
    display: flex;
    height: 100vh;
}

/* Sidebar */
.sidebar {
    background-color: #111;
    padding: 20px;
    width: 250px;
    position: fixed;
    height: 100%;
    top: 0;
    left: 0;
}

.sidebar h2,
.sidebar h3 {
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

/* Main Content */
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

.content-box {
    background-color: #333;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    color: #f1f1f1;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #444;
}

th {
    background-color: #444;
}

/* Botões */
.btn {
    padding: 5px 10px;
    background-color: #750a67;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    margin-right: 10px;
    display: inline-block;
}

.btn:hover {
    background-color: #9b0a88;
}

.btn-danger {
    background-color: #ff5555;
}

.btn-danger:hover {
    background-color: #ff3333;
}

/* Paginação */
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

/* Botões adicionais de ativar/desativar */
.btn-activate {
    background-color: #4CAF50;
}

.btn-activate:hover {
    background-color: #45a049;
}

.btn-deactivate {
    background-color: #f44336;
}

.btn-deactivate:hover {
    background-color: #da190b;
}

/* Links no conteúdo principal */
.main-content a {
    text-decoration: none;
    color: white;
}

.main-content a:hover {
    text-decoration: underline;
    color: #9b0a88;
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

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Gerenciar Cursos</h1>
        

        <div class="content-box">
            <h2>Lista de Cursos <a href="adicionar_cursos.php" class="btn">Adicionar Curso</a></h2>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Horário</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($row['horario']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                        <td>
                            <a href="editar_curso.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                            <a href="excluir_curso.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este curso?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div class="pagination">
                <?php if ($paginaAtual > 1): ?>
                    <a href="?pagina=<?php echo $paginaAtual - 1; ?>">&laquo; Anterior</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?php echo $i; ?>" <?php echo $i === $paginaAtual ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?pagina=<?php echo $paginaAtual + 1; ?>">Próxima &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</body>
</html>

<?php
$conn->close();
?>
