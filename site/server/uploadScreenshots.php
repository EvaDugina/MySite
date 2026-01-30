<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
}

if (isset($_FILES['screenshot'])) {
    $file = $_FILES['screenshot'];

    // Проверяем ошибки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Ошибка загрузки файла']);
        exit;
    }

    // Проверяем тип файла
    $allowedTypes = ['image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Неверный тип файла']);
        exit;
    }

    // Создаем папку, если не существует
    $uploadDir = '../uploads/screenshots/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Генерируем имя файла
    $filePath = $uploadDir . $file['name'];

    // Перемещаем файл
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Возвращаем успешный ответ
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Скриншот сохранен',
            'filename' => $file['name'],
            'url' => $filePath,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка сохранения файла']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Файл не найден в запросе']);
}
