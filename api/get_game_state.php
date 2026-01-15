<?php
// Получение текущего состояния игры в виде JSON
require_once "config.php";

$token   = $_POST['token']   ?? '';
// Идентификатор игры, состояние которой нужно получить
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

// Без валидного токена и game_id не работаем
if ($token === '' || $game_id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "No token or game_id"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT get_game_state_json(:token, :game_id) AS data
    ");
    $stmt->execute([
        ':token'   => $token,
        ':game_id' => $game_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от get_game_state_json
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from get_game_state_json"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}

