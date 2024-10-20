<?php
require_once 'conexao.php';

if (isset($_GET['login'])) {
    $login = $_GET['login'];

    // Fetch user details based on the login
    $query = "SELECT login, tipo_usuario, data_cadastro, email, photo FROM usuarios WHERE login = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'name' => $user['login'],
            'email' => $user['email'],
            'joinDate' => $user['data_cadastro'],
            'photo' => $user['photo'] // Assume you store user photo path in the database
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}
$conn->close();
?>
