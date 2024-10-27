<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está autenticado e é funcionário
function verificarAcesso() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.html");
        exit;
    }
}

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'cliente';
verificarAcesso();

if ($tipo_usuario !== 'funcionario') {
    header("Location: index2.php");
    exit;
}

// Verificar se o login do usuário foi fornecido na URL
if (!isset($_GET['login']) || empty($_GET['login'])) {
    echo "Usuário não encontrado.";
    exit;
}

// Obter o login do usuário
$login = $_GET['login'];

// Consulta para obter detalhes do usuário
$query = "SELECT login, nome, email, tipo_usuario, data_cadastro, ativo FROM usuarios WHERE login = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se o usuário foi encontrado
if ($result->num_rows === 0) {
    echo "Usuário não encontrado.";
    exit;
}

// Obter dados do usuário
$usuario = $result->fetch_assoc();
$tipo = $usuario['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Usuário - <?php echo htmlspecialchars($usuario['nome']); ?></title>
    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-color: #1f1f1f;
    color: #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.sidebar {
    background-color: #111;
    padding: 20px;
    width: 250px; /* Ajustar para mobile se necessário */
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

.ficha {
    background-color: #333;
    color: #f1f1f1;
    width: 700px;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0px 0px 15px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-left: 300px; /* Adicionando espaço à direita da sidebar */
}

.ficha .foto {
    width: 120px;
    height: 120px;
    background-color: #222;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ficha h1 {
    font-size: 1.8em;
    margin-bottom: 10px;
    color: #fafafa;
    text-align: center;
}

.dados-basicos, .secao-detalhes {
    width: 100%;
    padding: 20px;
    border: 1px solid #444;
    border-radius: 8px;
    margin-bottom: 20px;
    background-color: #2a2a2a;
}

.dados-basicos p, .secao-detalhes h2 {
    margin: 5px 0;
}

.secao-detalhes table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.secao-detalhes th, .secao-detalhes td {
    padding: 8px;
    border: 1px solid #555;
    text-align: left;
}

.secao-detalhes th {
    background-color: #444;
}

.btn {
    padding: 10px 20px;
    background-color: #750a67;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
    margin-top: 20px;
}

.btn:hover {
    background-color: #9b0a88;
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

    <div class="ficha">
        <div class="foto">
            <!-- Placeholder para a foto -->
            <img src="caminho/para/foto.jpg" alt="Foto do usuário" onerror="this.style.display='none';">
        </div>
        <h1><?php echo htmlspecialchars($usuario['nome']); ?></h1>

        <div class="dados-basicos">
            <p><strong>Login:</strong> <?php echo htmlspecialchars($usuario['login']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>Tipo de Usuário:</strong> <?php echo htmlspecialchars($usuario['tipo_usuario']); ?></p>
            <p><strong>Data de Cadastro:</strong> <?php echo htmlspecialchars($usuario['data_cadastro']); ?></p>
            <p><strong>Status:</strong> <?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?></p>
        </div>

        <?php if ($tipo === 'cliente'): ?>
            <div class="secao-detalhes">
                <h2>Cursos Inscritos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Data de Início</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryCursos = "SELECT c.nome, ic.data_inicio, ic.status 
                                        FROM inscricoes_cursos ic 
                                        JOIN cursos c ON ic.curso_id = c.id 
                                        WHERE ic.login_aluno = ?";
                        $stmtCursos = $conn->prepare($queryCursos);
                        $stmtCursos->bind_param("s", $login);
                        $stmtCursos->execute();
                        $resultCursos = $stmtCursos->get_result();

                        if ($resultCursos->num_rows > 0) {
                            while ($curso = $resultCursos->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($curso['nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($curso['data_inicio']) . "</td>";
                                echo "<td>" . htmlspecialchars($curso['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhum curso encontrado.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($tipo === 'funcionario'): ?>
            <div class="secao-detalhes">
                <h2>Últimas Ações</h2>
                <table>
                    <thead>
                        <tr>                            
                            <th>Data</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryAcoes = "SELECT id, data, descricao 
                                       FROM relatorios 
                                       WHERE usuario_cpf = ? 
                                       ORDER BY data DESC LIMIT 10";
                        $stmtAcoes = $conn->prepare($queryAcoes);
                        $stmtAcoes->bind_param("s", $login);
                        $stmtAcoes->execute();
                        $resultAcoes = $stmtAcoes->get_result();

                        if ($resultAcoes->num_rows > 0) {
                            while ($acao = $resultAcoes->fetch_assoc()) {
                                echo "<tr>";                                
                                echo "<td>" . htmlspecialchars($acao['data']) . "</td>";
                                echo "<td>" . htmlspecialchars($acao['descricao']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhuma ação registrada.</td></tr>";
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="gerenciar_usuarios.php" class="btn">Voltar</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
