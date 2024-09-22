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
    // Iniciar transação para garantir que ambas as inserções sejam feitas ou nenhuma
    $conn->begin_transaction();
    try {
        // Insere o usuário na tabela 'usuarios'
        $stmt1 = $conn->prepare("INSERT INTO usuarios (login, senha, data_cadastro) VALUES (?, ?, NOW())");
        $stmt1->bind_param("ss", $login, $senhaHash);
        $stmt1->execute();
        $stmt1->close();

        // Insere os dados do relatório na tabela 'relatorios' com o mesmo CPF (login)
        $stmt2 = $conn->prepare("INSERT INTO relatorios (usuario_cpf, data, descricao) VALUES (?, NOW(), 'Cadastro de Usuario')");
        $stmt2->bind_param("s", $login);
        $stmt2->execute();
        $stmt2->close();

        // Se ambos os INSERTs forem bem-sucedidos, confirma a transação
        $conn->commit();
        return ['success' => true, 'message' => 'Cadastro e relatório registrados com sucesso!'];

    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação
        $conn->rollback();
        error_log("Erro ao cadastrar: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao cadastrar o usuário e gerar o relatório.'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["cpf"];
    $senha = $_POST["senha"];

    // Validações básicas
    if (empty($login) || empty($senha)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!']);
        exit;
    }

    // Valida CPF e senha (mínimo 5 caracteres)
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
