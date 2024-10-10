<?php
$servername = "localhost";  // Host do banco de dados (geralmente é 'localhost')
$username = "root";  // Nome de usuário do banco de dados
$password = "";    // Senha do banco de dados
$dbname = "escolamusica";  // Nome do banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Opcional: definir o charset para evitar problemas com acentuação
$conn->set_charset("utf8");
?>
