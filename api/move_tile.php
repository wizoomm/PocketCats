<?php
// Обработка хода игрока, перемещение уже открытого тайла на новое место
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Читаем входные данные
$token    = $_POST['token']          ?? '';
$game_id  = isset($_POST['game_id'])  ? (int)$_POST['game_id']  : 0;
$target_x = isset($_POST['target_x']) ? (int)$_POST['target_x'] : 0;
$target_y = isset($_POST['target_y']) ? (int)$_POST['target_y'] : 0;

// Базовая валидация
if ($token === '' || $game_id <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'token or game_id missing'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $conn->prepare(
        'SELECT move_tile(:token, :game_id, :target_x, :target_y) AS data'
    );

    $stmt->execute([
        ':token'    => $token,
        ':game_id'  => $game_id,
        ':target_x' => $target_x,
        ':target_y' => $target_y,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data'];
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'No data from move_tile()'
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'DB error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}