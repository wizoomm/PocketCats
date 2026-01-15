<?php
// Авторизация пользователя по логину и паролю
require_once "config.php";

$login    = $_POST['login']    ?? '';
$password = $_POST['password'] ?? '';

// Проверка на пустые поля
if ($login === '' || $password === '') {
    echo json_encode(["status" => "error", "message" => "Empty login or password"]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT authorize_user(:login, :password) AS data
    ");
    $stmt->execute([
        ':login'    => $login,
        ':password' => $password
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data'];
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from authorize_user"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}
