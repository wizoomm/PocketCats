<?php
// Подключение игрока к существующей комнате
require_once "config.php";

$token   = $_POST['token']   ?? '';
// Id комнаты (игры), в которую пытаемся войти
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

// Проверяем, что пришёл и токен, и корректный game_id
if ($token === '' || $game_id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "No token or game_id"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT join_room(:token, :game_id) AS data
    ");
    $stmt->execute([
        ':token'   => $token,
        ':game_id' => $game_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от join_room
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from join_room"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}

