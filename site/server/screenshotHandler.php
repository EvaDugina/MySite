<?php
$UPLOAD_DIR = '../uploads/screenshots/';

function getScreenshotNames(string $patternName, string $ext): array
{
    global $UPLOAD_DIR;

    if (preg_match('/\.\.|\/|\\\\|\0/', $patternName)) {
        throw new InvalidArgumentException("Некорректное имя паттерна: содержит запрещенные символы");
    }

    // 5. Ограничение длины patternName (например, максимум 255 символов)
    if (strlen($patternName) > 255) {
        throw new InvalidArgumentException("Имя паттерна слишком длинное");
    }

    // 6. Валидация расширения файла
    // Разрешаем только буквы, цифры и некоторые безопасные символы
    if (!preg_match('/^[a-zA-Z0-9]{1,10}$/', $ext)) {
        throw new InvalidArgumentException("Некорректное расширение файла");
    }

    // 7. Подготовка пути - добавляем DIRECTORY_SEPARATOR
    $uploadDir = rtrim($UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR;

    // 8. Создание безопасного шаблона для поиска
    $pattern = $uploadDir . $patternName . '*.' . $ext;

    // 9. Дополнительная проверка, что итоговый шаблон не выходит за пределы $UPLOAD_DIR
    $realUploadDir = realpath($uploadDir);
    if ($realUploadDir === false) {
        throw new RuntimeException("Невозможно определить реальный путь директории");
    }

    return glob($pattern, GLOB_NOSORT);
}
