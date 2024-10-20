<?php
require_once 'conexao.php';

/**
 * Verifica se o usuário está ativo.
 *
 * @param string $login O login do usuário a ser verificado.
 * @return bool Retorna true se o usuário estiver ativo, false caso contrário.
 */
function verificarUsuarioAtivo($login) {
    global $conn;

    $query = "SELECT ativo FROM usuarios WHERE login = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return false; // Falha ao preparar a declaração
    }
    
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->bind_result($ativo);

    // Verifica se houve um resultado
    if ($stmt->fetch()) {
        $stmt->close();
        return $ativo === 1;
    }

    $stmt->close();
    return false; // Retorna false se nenhum resultado for encontrado
}
?>

