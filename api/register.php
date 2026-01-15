<?php
// Регистрация нового пользователя и выдача ему токена
require_once "config.php";

$login    = $_POST['login']    ?? '';
$password = $_POST['password'] ?? '';

// Проверка обязательных полей регистрации
if ($login === '' || $password === '') {
    echo json_encode(["status" => "error", "message" => "Empty login or password"]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT register_user(:login, :password) AS data
    ");
    $stmt->execute([
        ':login'    => $login,
        ':password' => $password
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['data'])) {
        echo $row['data']; // JSON от register_user (там сразу и токен)
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No data from register_user"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "DB error: " . $e->getMessage()
    ]);
}

