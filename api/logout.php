<?php
// Выход пользователя из системы (аннулирование токена)
require_once "config.php";

$token = $_POST['token'] ?? '';

// Если токен не передан, вернуть ошибку
if ($token === '') {
    echo json_encode([
        "status"  => "error",
        "message" => "Token not provided"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT logout_user(:token) AS data
    ");
    $stmt->execute([
        ':token' => $token
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от logout_user
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from logout_user"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}
