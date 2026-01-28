<?php
require_once("./fileHandler.php");

// Конфигурация
$dataFileName = __DIR__ . '/data/visitors.json';
$cookieName = 'visitor_id';
$cookieExpire = 30 * 24 * 3600; // 90 дней

// 1. Получаем или создаем ID посетителя
if (isset($_COOKIE[$cookieName])) {
    $visitorId = $_COOKIE[$cookieName];
} else {
    // Генерируем простой ID на основе времени и случайного числа
    $visitorId = 'visitor_' . time() . '_' . mt_rand(1000, 9999);
    setcookie($cookieName, $visitorId, time() + $cookieExpire, '/');
}

// 2. Читаем существующие данные
$data = json_decode(readFile($dataFileName), true);

// 3. Обновляем данные посетителя
if (!isset($data['visitors'][$visitorId])) {
    // Новый посетитель
    $data['visitors'][$visitorId] = [
        'created' => date('Y-m-d H:i:s'),
        'last_visit' => date('Y-m-d H:i:s'),
        'count_visits' => 1,
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100),
        'positionX' => null,
        'positionY' => null,
    ];
} else {
    // Существующий - обновляем
    $data['visitors'][$visitorId]['last_visit'] = date('Y-m-d H:i:s');
    $data['visitors'][$visitorId]['count_visits']++;
}

// 4. Сохраняем данные (простая защита от одновременной записи)
writeFile($dataFileName, json_encode($data, JSON_PRETTY_PRINT));

?>