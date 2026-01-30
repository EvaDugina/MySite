<?php
require_once __DIR__ . "/fileHandler.php";
$dataFileName = __DIR__ . '/data/visitors.json';

$cookieExpire = 90 * 24 * 3600; // 30 дней

function getVisitorsJson(): array | null
{
    global $dataFileName;
    return json_decode(readFileSafe($dataFileName), true);
}

function getVisitorPositions(): array | null
{
    $data = getVisitorsJson();
    foreach ($data as $key => $value) {
        unset($data[$key]['created']);
        unset($data[$key]['last_visit']);
        unset($data[$key]['count_click']);
    }

    return $data;
}

function createOrGenerateUUID(): string
{
    global $cookieExpire;
    $cookieName = 'OwSllZ44-UUID';

    if (isset($_COOKIE[$cookieName])) {
        $visitorId = $_COOKIE[$cookieName];
    } else {
        // Генерируем простой ID на основе времени и случайного числа
        $fingerprint = createSimpleFingerprint();
        $visitorId = 'visitor_' . $fingerprint;
        setcookie($cookieName, $visitorId, time() + $cookieExpire, '/');
    }

    return $visitorId;
}

function updateVisitorPosition(string $visitorId, float $positionX, float $positionY)
{
    global $dataFileName;

    $data = getVisitorsJson();

    if (!isset($data[$visitorId])) {
        // Новый посетитель
        $data[$visitorId] = [
            'positionX' => $positionX,
            'positionY' => $positionY,
            'created' => date('Y-m-d H:i:s'),
            'last_visit' => date('Y-m-d H:i:s'),
            'count_click' => 1,
        ];
    } else {
        // Существующий - обновляем
        $data[$visitorId]['positionX'] = $positionX;
        $data[$visitorId]['positionY'] = $positionY;
        $data[$visitorId]['last_visit'] = date('Y-m-d H:i:s');
        $data[$visitorId]['count_click']++;
    }

    writeFileSafe($dataFileName, json_encode($data, JSON_PRETTY_PRINT));
}

// Функция для создания простого fingerprint (без IP!)
function createSimpleFingerprint(): string
{
    $parts = [
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'unknown',
        time()
    ];

    // Создаем хеш из этих данных
    return md5(implode('|', $parts));
}
