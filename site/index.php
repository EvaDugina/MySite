<?php
require_once("constants.php");
require_once("utilities.php");

function isFolderHidden(string $folder_name): bool
{
    return str_contains($folder_name, "~");
}

if (!file_exists($FOLDER_SRC) || !is_dir($FOLDER_SRC))
    mkdir($FOLDER_SRC, 0777, true);

// Создание разделов
$chapters_and_subchapters = [];
$files = array_diff(scandir($FOLDER_SRC), array('.', '..'));
$files = sortFiles($files);
foreach ($files as $chapter_file) {
    $chapter_file_path = $FOLDER_SRC . "/" . $chapter_file;
    if (is_dir($chapter_file_path)) {
        $chapter_name = $chapter_file;

        if (isFolderHidden($chapter_name))
            continue;

        $subchapter_files = array_diff(scandir($chapter_file_path), array('.', '..'));
        $files = sortFiles($files);
        // if (count($subchapter_files) < 1)
        // continue;
        $subchapter_names = [];
        foreach ($subchapter_files as $subchapter_file) {
            $subchapter_file_path = $chapter_file_path . "/" . $subchapter_file;
            if (is_dir($subchapter_file_path)) {
                if (isFolderHidden($subchapter_file_path))
                    continue;
                array_push($subchapter_names, $subchapter_file);
            }
        }
        $chapters_and_subchapters = array_merge($chapters_and_subchapters, [
            $chapter_name => [
                "subchapter_names" => $subchapter_names
            ]
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PALKHHHHHHHHHHHHH</title>

    <!-- Иконка -->
    <link rel="icon" href="src/icon.jpg" type="image/x-icon">
    <link rel="shortcut icon" href="src/icon.jpg" type="image/x-icon">

    <!-- Подключение внешнего файла -->
    <link rel="stylesheet" href="index.css">
</head>

<body class="overflow-y">

    <div class="d-flex flex-row fixed-panel w-100">

        <div class="w-50 me-2">

            <div class="d-flex flex-row justify-content-end">
                <h4>ПАЛЬХ</h4>
            </div>

            <div class="d-flex flex-row justify-content-end mb-2">

                <?php
                foreach ($chapters_and_subchapters as $key => $chapter) {
                ?>
                    <div class="form_radio_btn ms-1" onclick="clickToChapter('<?= $key ?>')">
                        <input id="input-radio-<?= $key ?>" type="radio"
                            name="input-radio" value="<?= $key ?>">
                        <label for="input-radio-<?= $key ?>"><?= getFolderBasename($key) ?></label>
                    </div>
                <?php
                } ?>

            </div>

            <div id="div-chapters-description">
                <?php
                foreach ($chapters_and_subchapters as $key => $chapter) { ?>
                    <div id="div-<?= $key ?>" class="d-flex flex-column d-none mt-1">
                        <?php
                        foreach ($chapter['subchapter_names'] as $subchapter_name) { ?>
                            <div class="d-flex flex-row justify-content-end">
                                <button class="link text-secondary mb-1" onclick="clickToSubchapter('<?= $subchapter_name ?>')">
                                    <?= getFolderBasename($subchapter_name) ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>

        <div class="w-100"></div>

    </div>

    <div class="d-flex flex-row w-100" style="position:absolute;">

        <div class="w-50 me-2"></div>

        <div id="div-view" class="w-100 z-2">

            <div id="div-view-chapter" class="d-flex flex-column"></div>
            <div id="div-view-subchapter" class="d-flex flex-column"></div>

        </div>
    </div>

    </div>


</body>

<script src="src/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    var CURRENT_CHAPTER = null;
    const CHAPTERS_AND_SUBCHAPTERS = <?= json_encode($chapters_and_subchapters) ?>;

    function isCorrectChapter(chapter_name) {
        let flag = false;
        Object.entries(CHAPTERS_AND_SUBCHAPTERS).forEach(([key, value]) => {
            if (key == chapter_name) {
                flag = true;
                return;
            }
        });
        return flag;
    }

    async function clickToChapter(chapter_name) {

        if (!isCorrectChapter(chapter_name))
            return;

        Array.from(document.getElementById("div-chapters-description").children).forEach(child => {
            child.classList.add("d-none");
        });
        document.getElementById("div-" + chapter_name).classList.remove("d-none");
        CURRENT_CHAPTER = chapter_name;

        let ajaxResponse = await ajaxGetSubChapterHtml(chapter_name);
        if (ajaxResponse == null) {
            return;
        }

        document.getElementById("div-view-chapter").innerHTML = ajaxResponse;
    }

    function isCorrectSubchapter(subchapter_name) {
        let flag = false;
        // Object.entries(CHAPTERS_AND_SUBCHAPTERS[CURRENT_CHAPTER]).forEach(([key, value]) => {});
        CHAPTERS_AND_SUBCHAPTERS[CURRENT_CHAPTER]['subchapter_names'].forEach(element => {
            if (element == subchapter_name) {
                flag = true;
                return;
            }
        });
        return flag;
    }

    async function clickToSubchapter(subchapter_name) {

        if (CURRENT_CHAPTER == null || !isCorrectChapter(CURRENT_CHAPTER))
            return;
        chapter_name = CURRENT_CHAPTER;

        if (!isCorrectSubchapter(subchapter_name))
            return;

        let ajaxResponse = await ajaxGetSubChapterHtml(chapter_name, subchapter_name);
        if (ajaxResponse == null) {
            return;
        }

        document.getElementById("div-view-subchapter").innerHTML = ajaxResponse;
        scrollTop();
    }

    function scrollTop() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    async function ajaxGetSubChapterHtml(chapter_name, subchapter_name = null) {

        var formData = new FormData();
        formData.append('chapter_name', chapter_name);
        if (subchapter_name != null)
            formData.append('subchapter_name', subchapter_name);

        let response = null;
        try {
            response = await $.ajax({
                type: "POST",
                url: 'view.php#content',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType: 'html'
            });
            console.log(response.trim());
            response = response.trim();
        } catch (error) {
            console.error('Ошибка запроса:', error);
            return null;
        }

        return response;
    }
</script>

</html>