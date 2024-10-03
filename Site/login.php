<?php
session_start(); // Inicia a sessão para armazenar tentativas de login

// Limite de tentativas e tempo de bloqueio (em segundos)
$max_attempts = 3;
$block_duration = 10 * 60; // 10 minutos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Remove caracteres não numéricos do CPF
    $login = preg_replace("/[^0-9]/", "", $login);

    // Inicializa as tentativas na sessão se não existir
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    // Verifica se o usuário está bloqueado
    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
        if ($time_since_last_attempt < $block_duration) {
            $remaining_time = ceil(($block_duration - $time_since_last_attempt) / 60);
            header("Location: login.html?error=Você está bloqueado. Tente novamente em $remaining_time minutos.");
            exit;
        } else {
            // Reseta as tentativas após o tempo de bloqueio expirar
            $_SESSION['login_attempts'] = 0;
        }
    }

    // Validação básica
    if (empty($login) || empty($senha)) {
        $_SESSION['login_attempts'] += 1;
        $_SESSION['last_attempt_time'] = time(); // Atualiza o tempo da última tentativa
        header("Location: login.html?error=Preencha todos os campos");
        exit;
    }

    // Conexão ao banco de dados
    $conn = new mysqli("localhost", "root", "", "escolamusica");
    if ($conn->connect_error) {
        header("Location: login.html?error=Erro na conexão com o banco de dados");
        exit;
    }

    // Prepara a consulta SQL
    $stmt = $conn->prepare("SELECT senha, tipo_usuario FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();

    // Se encontrou o usuário
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $tipo_usuario);
        $stmt->fetch();

        // Verifica se a senha está correta
        if (password_verify($senha, $hashed_password)) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $login;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            header("Location: index2.php"); // Redireciona para a página principal
            exit;
        } else {
            // Incrementa tentativas em caso de senha inválida
            $_SESSION['login_attempts'] += 1;
            $_SESSION['last_attempt_time'] = time();
            header("Location: login.html?error=Credenciais inválidas");
            exit;
        }
    } else {
        // Incrementa tentativas em caso de usuário não encontrado
        $_SESSION['login_attempts'] += 1;
        $_SESSION['last_attempt_time'] = time();
        header("Location: login.html?error=Usuário não encontrado");
        exit;
    }

    // Fecha o statement e a conexão com o banco de dados
    $stmt->close();
    $conn->close();
}
?>
