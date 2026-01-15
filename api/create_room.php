<?php
// Создание новой игровой комнаты
require_once "config.php";

$token     = $_POST['token'] ?? '';
// Время на ход (по умолчанию 30 секунд, если не передано)
$step_time = isset($_POST['step_time']) ? (int)$_POST['step_time'] : 30;

// Без токена создавать комнату нельзя
if ($token === '') {
    echo json_encode([
        "status"  => "error",
        "message" => "No token"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT create_room(:token, :step_time) AS data
    ");
    $stmt->execute([
        ':token'     => $token,
        ':step_time' => $step_time
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от create_room
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from create_room"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}
