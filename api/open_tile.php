<?php
// Открытие тайла по координатам на поле (первое действие хода)
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

$token   = $_POST['token']          ?? '';
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;
// Координаты клетки, которую хотим открыть
$x       = isset($_POST['x'])       ? (int)$_POST['x']       : 0;
$y       = isset($_POST['y'])       ? (int)$_POST['y']       : 0;

try {
    $stmt = $conn->prepare('SELECT open_tile(:token, :game_id, :x, :y) AS data');
    $stmt->execute([
        ':token'   => $token,
        ':game_id' => $game_id,
        ':x'       => $x,
        ':y'       => $y,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['data'])) {
        echo $row['data'];
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'No data from open_tile()'
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'DB error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

