<?php
// Получение списка доступных комнат для текущего пользователя
require_once "config.php";

$token = $_POST['token'] ?? '';

// Без токена нельзя показать список комнат
if ($token === '') {
    echo json_encode([
        "status"  => "error",
        "message" => "No token"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT get_rooms_json(:token) AS data
    ");
    $stmt->execute([
        ':token' => $token
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON: {status, rooms:[{game_id, opponent}]}
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from get_rooms_json"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}
