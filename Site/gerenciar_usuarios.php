<?php
session_start();




require_once 'conexao.php'; // Arquivo de conexão com o banco de dados
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';

// Função para verificar se o usuário é funcionário autenticado
function verificarAcesso() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.html");
        exit;
    }
}

// Função para exibir uma mensagem de sucesso
function obterMensagemSucesso() {
    return isset($_GET['updated']) && $_GET['updated'] === 'true' ? 'Usuário atualizado com sucesso!' : '';
}

// Verifica acesso do usuário
verificarAcesso();

// Apenas funcionários podem acessar a página de relatórios
if ($tipo_usuario !== 'funcionario') {
    header("Location: index2.php"); // Redireciona para o painel principal se não for funcionário
    exit;
}
// Obtém mensagem de sucesso
$mensagem_sucesso = obterMensagemSucesso();

// Função para obter a mensagem de acordo com os parâmetros da URL
function obterMensagem() {
    if (isset($_GET['deactivated']) && $_GET['deactivated'] === 'true') {
        return ['Usuário desativado com sucesso!', 'success'];
    }
    if (isset($_GET['deleted']) && $_GET['deleted'] === 'true') {
        return ['Usuário excluído com sucesso!', 'success'];
    }
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'missing_login':
                return ['Login do usuário não foi fornecido.', 'error'];
            case 'delete_failed':
                return ['Erro ao tentar excluir o usuário.', 'error'];
            case 'deactivate_failed':
                return ['Erro ao tentar desativar o usuário.', 'error'];
            case 'inactive_user':
                return ['Usuário está inativo e não pode acessar o sistema.', 'error'];
            default:
                return ['Ocorreu um erro desconhecido.', 'error'];
        }
    }
    return [null, null]; // Sem mensagem para exibir
}

// Obtém a mensagem e o tipo
list($mensagem, $tipoMensagem) = obterMensagem();

// Configurações de paginação
$usuariosPorPagina = 10; // Número de usuários por página
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página atual
$offset = ($paginaAtual - 1) * $usuariosPorPagina; // Cálculo do offset

// Consulta para contar o total de usuários
$totalUsuariosQuery = "SELECT COUNT(*) as total FROM usuarios";
$totalUsuariosResult = $conn->query($totalUsuariosQuery);
$totalUsuarios = $totalUsuariosResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalUsuarios / $usuariosPorPagina);

// Consulta para buscar os usuários com limite e offset
$query = "SELECT login, tipo_usuario, data_cadastro FROM usuarios LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $usuariosPorPagina, $offset);
$stmt->execute();
$result = $stmt->get_result();

$status = isset($row['ativo']) ? $row['ativo'] : 'Indefinido';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
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

        .main-content {
            margin-left: 300px;
            padding: 40px;
            background-color: #2b2b2b;
            flex: 1;
            overflow-y: auto;
            height: 100%;
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

        table {
            width: 100%;
            border-collapse: collapse;
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

        .btn {
            padding: 5px 10px;
            background-color: #750a67;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
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

        .btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; margin-right: 10px; color: white; }
        .btn-activate { background-color: #4CAF50; }
        .btn-activate:hover { background-color: #45a049; }
        .btn-deactivate { background-color: #f44336; }
        .btn-deactivate:hover { background-color: #da190b; }

    
    /* Modal styles */
    #userModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #333;
            color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            z-index: 1000;
            width: 300px;
        }

        #modalOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 900;
        }

        .modal-content {
            text-align: center;
        }

        .close-btn {
            cursor: pointer;
            float: right;
            font-size: 20px;
            margin-top: -10px;
            margin-right: -10px;
        }
    </style>
</head>
<body>

    <!-- Modal -->

<div id="modalOverlay"></div>

<div id="userModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <img id="userPhoto" src="" alt="User Photo" style="width: 100px; height: 100px; border-radius: 50%;">
        <h3 id="userName"></h3>
        <p id="userEmail"></p>
        <p id="userJoinDate"></p>
    </div>
</div>

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

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h1>Gerenciar Usuários</h1>
        <?php if ($mensagem): ?>
            <div class="message" style="padding: 15px; border-radius: 4px; margin-bottom: 20px;"<?php echo $tipoMensagem; ?>">
                <p><?php echo htmlspecialchars($mensagem); ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensagem_sucesso)): ?>
            <div class="content-box" style="background-color: #4CAF50; color: white;">
                <p><?php echo htmlspecialchars($mensagem_sucesso); ?></p>
            </div>
        <?php endif; ?>

        <div class="content-box">
            <h2>Lista de Usuários</h2>
            <table>
            <tr>
                    <th>Login</th>
                    <th>Tipo de Usuário</th>
                    <th>Data de Cadastro</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td><a href="#" onclick="openModal('<?php echo htmlspecialchars($row['login']); ?>'); console.log('Clicked:', '<?php echo htmlspecialchars($row['login']); ?>');"><?php echo htmlspecialchars($row['login']); ?></a></td>
                    <td><?php echo htmlspecialchars($row['tipo_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($row['data_cadastro']); ?></td>
                    <td><?php echo isset($status) ? ($status ? 'Ativo' : 'Inativo') : 'Indefinido'; ?></td>                
                        <td>
                        <a href="editar_usuario.php?login=<?php echo $row['login']; ?>" class="btn">Editar</a>
                            <?php if ($status): ?>
                                <a href="desativar_usuario.php?login=<?php echo urlencode($row['login']); ?>" class="btn btn-deactivate" onclick="return confirm('Tem certeza que deseja desativar este usuário?');">Desativar</a>
                            <?php else: ?>
                                <a href="ativar_usuario.php?login=<?php echo urlencode($row['login']); ?>" class="btn btn-activate" onclick="return confirm('Tem certeza que deseja ativar este usuário?');">Ativar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div class="pagination">
                <?php if ($paginaAtual > 1): ?>
                    <a href="?pagina=<?php echo $paginaAtual - 1; ?>">&laquo; Anterior</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?php echo $i; ?>" <?php echo $i === $paginaAtual ? 'style="background-color: #9b0a88;"' : ''; ?>>
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
<script>
    function openModal(login) {
        fetch(`receber_detalhes_gerenciar.php?login=${encodeURIComponent(login)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('userPhoto').src = data.photo;
                    document.getElementById('userName').textContent = data.name;
                    document.getElementById('userEmail').textContent = data.email;
                    document.getElementById('userJoinDate').textContent = `Joined: ${data.joinDate}`;
                    
                    document.getElementById('userModal').style.display = 'block';
                    document.getElementById('modalOverlay').style.display = 'block';
                } else {
                    console.error('Error: No data received.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }
</script>

</html>

<?php
$conn->close();
?>
