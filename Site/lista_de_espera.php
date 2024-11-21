<?php
// Configuração de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escolamusica";

// Estabelece a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Função para calcular a posição na fila
function getFilaPosicao($id, $conn) {
    // Consultar a posição do usuário na fila
    $sql = "SELECT COUNT(*) as posicao FROM pre_matricula WHERE tipo_inscricao IN ('individual', 'grupo') AND data_nascimento != '' AND id <= $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['posicao'];
}

// Obtém a lista de usuários na fila de espera
$sql = "SELECT * FROM pre_matricula WHERE tipo_inscricao IN ('individual', 'grupo') AND data_nascimento != '' ORDER BY id ASC";
$result = $conn->query($sql);

// Variável para armazenar a lista de usuários e suas posições
$usuarios = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $posicao = getFilaPosicao($row['id'], $conn); // Passando a conexão para a função getFilaPosicao
        $row['posicao'] = $posicao;
        $usuarios[] = $row; // Armazena o usuário e sua posição na fila
    }
}

// Fecha a conexão com o banco de dados após as consultas
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Espera</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        .progress-bar-container {
            background-color: #ddd;
            border-radius: 20px;
            overflow: hidden;
            margin: 20px 0;
            height: 20px;
            width: 100%;
        }
        .progress-bar {
            height: 100%;
            width: 0;
            background-color: #4caf50;
            transition: width 1s;
        }
        .user-list {
            list-style-type: none;
            padding: 0;
        }
        .user-list li {
            padding: 15px;
            background-color: #fafafa;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .user-name {
            font-size: 18px;
            font-weight: bold;
        }
        .user-email {
            color: #007bff;
        }
        .position {
            color: #555;
            font-size: 14px;
            margin-top: 5px;
        }
        .status {
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Lista de Espera - Status da Matrícula</h1>

    <?php if (count($usuarios) > 0): ?>
        <ul class="user-list">
            <?php foreach ($usuarios as $row): ?>
                <?php 
                $progresso = 0; // Inicializamos a porcentagem de progresso
                if ($row['data_nascimento'] !== '') {
                    $progresso = 25; // Processo de pré-matrícula concluído
                }
                if ($row['rg'] !== '') {
                    $progresso = 50; // Validação de dados concluída
                }
                if ($row['periodo_aulas'] !== '') {
                    $progresso = 75; // Lista de espera confirmada
                }
                if ($row['tipo_inscricao'] !== '') {
                    $progresso = 100; // Confirmação completa
                }
                ?>
                <li>
                    <div class="user-name"><?= htmlspecialchars($row['nome']); ?></div>
                    <div class="user-email"><?= htmlspecialchars($row['email']); ?></div>
                    <div class="position">Posição na fila: #<?= $row['posicao']; ?></div>

                    <!-- Barra de progresso -->
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $progresso; ?>%;"></div>
                    </div>

                    <div class="status">Status: 
                        <?php 
                            if ($progresso == 0) {
                                echo 'Aguardando dados.';
                            } elseif ($progresso == 25) {
                                echo 'Pré-matrícula concluída.';
                            } elseif ($progresso == 50) {
                                echo 'Validação de dados concluída.';
                            } elseif ($progresso == 75) {
                                echo 'Aguardando na lista de espera.';
                            } else {
                                echo 'Matrícula confirmada.';
                            }
                        ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum usuário na lista de espera.</p>
    <?php endif; ?>
</div>

</body>
</html>
