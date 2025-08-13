<?php
require_once("constants.php");
require_once("utilities.php");

// Проверка корректности входных данных

if (isset($_POST['chapter_name'])) {
    $chapter_folder_path = $FOLDER_SRC . "/" . $_POST['chapter_name'];
} else {
    // echo "Ошибка! Не задан 'chapter_name'";
    exit;
}
if (is_not_chapter_exist($chapter_folder_path)) {
    // echo "Ошибка! Не корректный 'chapter_name'";
    exit;
}

if (isset($_POST['subchapter_name'])) {
    $subchapter_name = $_POST['subchapter_name'];
} else {
    $subchapter_name = "~";
}
$subchapter_folder_path = $chapter_folder_path . "/" . $subchapter_name;

if (!file_exists($subchapter_folder_path) || !is_dir($subchapter_folder_path)) {
    exit;
}

// Подготовка

$CONFIG_JSON = json_decode(file_get_contents("./config.json"), true);

// Берем файлы

$subchapter_files = [];
$files = array_diff(scandir($subchapter_folder_path), array('.', '..'));
$files = sortFiles($files);
foreach ($files as $file_name) {
    $file_path = $subchapter_folder_path . "/" . $file_name;
    if (is_dir($file_path))
        continue;
    $file_info = pathinfo($file_path);
    if (!array_key_exists($file_info['extension'], $CONFIG_JSON))
        continue;

    $basename = getFileBasename($file_path);
    if ($basename == "Описание") {
        array_unshift($subchapter_files, [
            "name" => $basename,
            "path" => $file_path,
            "type" => $CONFIG_JSON[$file_info['extension']]['type'],
            "isDescription" => True
        ]);
    } else {
        array_push($subchapter_files, [
            "name" => $basename,
            "path" => $file_path,
            "type" => $CONFIG_JSON[$file_info['extension']]['type'],
            "isDescription" => False
        ]);
    }
}

// Отображаем файлы
?>

<div class="d-flex flex-column w-100 mt-1">

    <?php
    $filter = null;
    foreach ($subchapter_files as $file) {

        // var_dump($file);
        // echo "<br>";
        if ($file['type'] == "text") {
            $filter = null;
            $content = trim(file_get_contents($file['path']));
            if ($file['isDescription'] && $content == "")
                continue;
    ?>

            <h3><?= $file['name'] ?></h3>

            <?php if ($content == "")
                continue; ?>

            <p class="w-100 text mb-1"><?= str_replace("\n", "<br>", $content) ?></p>

        <?php } else if ($file['type'] == "json") {
            $filter = null;
            $content_json = json_decode(trim(file_get_contents($file['path'])), true);
            if (isset($content_json["filter"]))
                $filter = $content_json["filter"];
        } else if ($file['type'] == "image") {
            list($width, $height, $type, $attr) = getimagesize($file['path']);
            $width = (int) $width;
            $height = (int) $height;
            $width_class = "w-50";
            if ($width * 2 >= $height * 3)
                $width_class = "w-75";

            if ($filter === null)
                $filter = "img-saturate-2";
        ?>

            <img class="<?= $filter ?> <?= $width_class ?> mb-1 me-1" src="<?= $file['path'] ?>" alt="<?= $file['name'] ?>"></img>

    <?php }
    } ?>

</div>