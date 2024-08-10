<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'escolamusica');

function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {  // Loop para calcular os dois dígitos verificadores
    $d = 0;                    // Inicializa a soma
    for ($c = 0; $c < $t; $c++) {   // Loop para multiplicar os dígitos pelos pesos
        $d += $cpf[$c] * (($t + 1) - $c);  // Multiplica cada dígito por um peso decrescente
    }
    $d = ((10 * $d) % 11) % 10;  // Calcula o dígito verificador
    if ($cpf[$t] != $d) {        // Verifica se o dígito calculado é igual ao fornecido
        return false;  // Se não for, o CPF é inválido
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

function cadastrarUsuario($login, $senhaHash, $conn) {
    if ($stmt = $conn->prepare("INSERT INTO usuarios (login, senha) VALUES (?, ?)")) {
        $stmt->bind_param("ss", $login, $senhaHash);
        if ($stmt->execute() === TRUE) {
            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
        } else {
            error_log("Erro ao cadastrar: " . $stmt->error);
            return ['success' => false, 'message' => 'Erro ao cadastrar o usuário.'];
        }
        $stmt->close();
    } else {
        error_log("Erro ao preparar a consulta: " . $conn->error);
        return ['success' => false, 'message' => 'Erro interno ao processar o cadastro.'];
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    if (empty($login) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!']);
        exit;
    }

    if (strlen($login) != 11 || strlen($senha) < 5) {
        echo json_encode(['success' => false, 'message' => 'CPF ou senha inválidos.']);
        exit;
    }

    if (!validaCPF($login)) {
        echo json_encode(['success' => false, 'message' => 'CPF inválido.']);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $conn = conectarBancoDeDados();
    if ($conn === null) {
        echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados.']);
        exit;
    }

    $resultado = cadastrarUsuario($login, $senhaHash, $conn);
    echo json_encode($resultado);

    $conn->close();
}
?>
