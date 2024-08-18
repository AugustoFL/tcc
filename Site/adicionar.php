<?php
header('Content-Type: application/json');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'escolamusica');

function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$t] != $d) {
            return false;
        }
    }
    return true;
}

function conectarBancoDeDados() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        error_log("Erro na conexão: " . $conn->connect_error);
        return null;
    }
    return $conn;
}

function verificarCPFExistente($login, $conn) {
    $stmt = $conn->prepare("SELECT login FROM usuarios WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

function cadastrarUsuario($login, $senhaHash, $conn) {
    if ($stmt = $conn->prepare("INSERT INTO usuarios (login, senha) VALUES (?, ?)")) {
        $stmt->bind_param("ss", $login, $senhaHash);
        if ($stmt->execute() === TRUE) {
            $stmt->close();
            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
        } else {
            error_log("Erro ao cadastrar: " . $stmt->error);
            $stmt->close();
            return ['success' => false, 'message' => 'Erro ao cadastrar o usuário.'];
        }
    } else {
        error_log("Erro ao preparar a consulta: " . $conn->error);
        return ['success' => false, 'message' => 'Erro interno ao processar o cadastro.'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    if (empty($login) || empty($senha)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!']);
        exit;
    }

    if (strlen($login) != 11 || strlen($senha) < 5) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'CPF ou senha inválidos.']);
        exit;
    }

    if (!validaCPF($login)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'CPF inválido.']);
        exit;
    }

    $conn = conectarBancoDeDados();
    if ($conn === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados.']);
        exit;
    }

    if (verificarCPFExistente($login, $conn)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'CPF já cadastrado.']);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
    $resultado = cadastrarUsuario($login, $senhaHash, $conn);
    echo json_encode($resultado);

    $conn->close();
}
?>