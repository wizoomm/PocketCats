<?php
// Выход игрока из текущей игры (комнаты)
require_once "config.php";

$token   = $_POST['token']   ?? '';
// Id игры, из которой выходим
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

// Без токена и корректного game_id выход невозможен
if ($token === '' || $game_id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "No token or game_id"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT leave_game(:token, :game_id) AS data
    ");
    $stmt->execute([
        ':token'   => $token,
        ':game_id' => $game_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data'];
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from leave_game"
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
