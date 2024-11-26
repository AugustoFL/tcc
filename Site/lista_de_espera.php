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

// Consulta SQL para obter os dados organizados por curso
$query = "
    SELECT 
        c.nome AS curso_nome,
        m.nome AS aluno_nome,
        m.status AS aluno_status,
        m.data_inscricao,
        IF(m.status = 'Lista de Espera', 
           ROW_NUMBER() OVER (PARTITION BY m.course_id ORDER BY m.data_inscricao), 
           NULL
        ) AS posicao_lista
    FROM matricula m
    JOIN cursos c ON m.course_id = c.id
    ORDER BY 
        c.nome,
        CASE 
            WHEN m.status = 'Matricula Confirmada' THEN 1
            WHEN m.status = 'Lista de Espera' THEN 2
            ELSE 3
        END,
        m.data_inscricao;";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Espera</title>
    <style>
        /* Estilo principal */
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            margin-top: 40px;
            padding: 20px;
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        h1, h2 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        h2 {
            margin-top: 20px;
        }
        .step-progress {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .step {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background-color: #252525;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .step.active {
            background-color: #4070f4;
            color: #fff;
        }
        .step.confirmed {
            background-color: #28a745;
            color: #fff;
        }
        .step.inactive {
            background-color: #333;
            color: #888;
        }
        .step .position {
            font-size: 14px;
            font-weight: 300;
        }
        .step .status {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Espera por Curso</h1>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php 
            $curso_atual = null; 
            while ($row = $result->fetch_assoc()): 
            ?>
                <?php if ($curso_atual !== $row['curso_nome']): ?>
                    <?php if ($curso_atual !== null): ?>
                        </div> <!-- Fecha lista anterior -->
                    <?php endif; ?>
                    <h2>Curso: <?= htmlspecialchars($row['curso_nome']) ?></h2>
                    <div class="step-progress">
                    <?php $curso_atual = $row['curso_nome']; ?>
                <?php endif; ?>

                <div class="step <?= $row['aluno_status'] === 'Matricula Confirmada' ? 'confirmed' : ($row['aluno_status'] === 'Lista de Espera' ? 'active' : 'inactive') ?>">
                    <span class="status">
                        <?= htmlspecialchars($row['aluno_nome']) ?> - <?= htmlspecialchars($row['aluno_status']) ?>
                    </span>
                    <?php if ($row['aluno_status'] === 'Lista de Espera'): ?>
                        <span class="position">Posição: <?= htmlspecialchars($row['posicao_lista']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div> <!-- Fecha última lista -->
        <?php else: ?>
            <p>Nenhuma lista de espera encontrada.</p>
        <?php endif; ?>
    </div>
</body>
</html>
