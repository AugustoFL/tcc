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

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM cursos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: gerenciar_cursos.php?msg=Curso+excluido+com+sucesso");
    } else {
        header("Location: gerenciar_cursos.php?msg=Erro+ao+excluir+curso");
    }
    
    $stmt->close();
} else {
    header("Location: gerenciar_cursos.php");
}

$conn->close();
?>
