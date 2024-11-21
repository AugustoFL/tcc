<?php
    header('Content-Type: application/json');
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "escolamusica";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die(json_encode(["error" => "Falha na conexão com o banco de dados"]));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
    
        $sql = "SELECT id, tipo_inscricao FROM pre_matricula WHERE email = ? AND tipo_inscricao = 'lista_espera'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
    
            // Determinar posição na fila
            $sqlQueue = "SELECT COUNT(*) AS position FROM pre_matricula WHERE tipo_inscricao = 'lista_espera' AND id <= ?";
            $stmtQueue = $conn->prepare($sqlQueue);
            $stmtQueue->bind_param("i", $row['id']);
            $stmtQueue->execute();
            $positionResult = $stmtQueue->get_result();
            $positionRow = $positionResult->fetch_assoc();
    
            // Retornar posição e progresso
            echo json_encode([
                "position" => $positionRow['position'],
                "stage" => 3, // Etapa na barra de progresso (3 = lista de espera)
            ]);
        } else {
            echo json_encode(["error" => "E-mail não encontrado na lista de espera"]);
        }
    
        $stmt->close();
    }
    
    $conn->close();
    ?>