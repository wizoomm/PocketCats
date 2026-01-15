<?php
// Пропуск хода текущим игроком
require_once "config.php";

$token   = $_POST['token']   ?? '';
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

// Без токена и id игры пропустить ход нельзя
if ($token === '' || $game_id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "token or game_id missing"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT skip_turn(:token, :game_id) AS data
    ");
    $stmt->execute([
        ':token'   => $token,
        ':game_id' => $game_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от skip_turn
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "no data from skip_turn"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}

