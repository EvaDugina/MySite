<?php
require_once __DIR__ . "/visitorHandler.php";

// Очистка записей старше $cleanTimeout дней (опционально)
// $cleanTimeout = time() - $cookieExpire;
// foreach ($data['visitors'] as $id => $visitor) {
//     if (strtotime($visitor['last_visit']) < $cleanTimeout) {
//         unset($data['visitors'][$id]);
//     }
// }

$returnJson = array(
    "exitCode" => -1
);

if (isset($_POST['flag-updateVisitorData'])) {
    if (isset($_POST['uuid']) && isset($_POST['positionX']) && isset($_POST['positionY'])) {
        updateVisitor($_POST['uuid'], (float)$_POST['positionX'], (float)$_POST['positionY']);
        $returnJson['exitCode'] = 0;
    }
    $returnJson['exitCode'] = 1;
    echo json_encode($returnJson);
    exit;
}
