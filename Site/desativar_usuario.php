<?php
session_start();

require_once 'conexao.php';

function verificarAcesso() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['tipo_usuario'] !== 'funcionario') {
        header("Location: login.html");
        exit;
    }
}

verificarAcesso();

if (!isset($_GET['login']) || empty($_GET['login'])) {
    header("Location: gerenciar_usuarios.php?error=missing_login");
    exit;
}

$login = $_GET['login'];

$query = "UPDATE usuarios SET ativo = 0 WHERE login = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $login);

if ($stmt->execute()) {
    header("Location: gerenciar_usuarios.php?deactivated=true");
} else {
    header("Location: gerenciar_usuarios.php?error=deactivate_failed");
}

$stmt->close();
$conn->close();
exit;
