<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Метод не разрешён']);
    exit;
}

if (!empty($_POST['website'])) {
    echo json_encode(['success' => false, 'message' => 'Спам-защита сработала']);
    exit;
}

$formTime = isset($_POST['formtime']) ? (int)$_POST['formtime'] : 0;
$currentTime = time() * 1000;
if ($formTime > 0 && ($currentTime - $formTime) < 3000) {
    echo json_encode(['success' => false, 'message' => 'Форма заполнена слишком быстро']);
    exit;
}

if (empty($_POST['token']) || strlen($_POST['token']) < 10) {
    echo json_encode(['success' => false, 'message' => 'Недействительный запрос']);
    exit;
}

$name = htmlspecialchars($_POST['name'] ?? '');
$phone = htmlspecialchars($_POST['phone'] ?? '');
$message = htmlspecialchars($_POST['message'] ?? '');

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Заполните имя и телефон']);
    exit;
}

if (!preg_match('/^8\s\d{3}\s\d{3}\s\d{2}\s\d{2}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Телефон в формате 8 999 999 99 99']);
    exit;
}

$token = "7605517716:AAHsn2av8z3XcihQ437VPEuwpnDARstYfO0";
$chat_id = "2039389573";
$text = "📩 Новое сообщение:\nИмя: $name\nТелефон: $phone\nСообщение: $message";

$send = file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($text));

if ($send) {
    echo json_encode(['success' => true, 'message' => 'Спасибо! Мы вам перезвоним.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при отправке в Telegram.']);
}
exit;