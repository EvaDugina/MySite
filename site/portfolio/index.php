<?php
require_once("./constants.php");
require_once("./utilities.php");

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
        $chapter_path = $chapter_file;

        if (isFolderHidden($chapter_path))
            continue;

        $subchapter_files = array_diff(scandir($chapter_file_path), array('.', '..'));
        $files = sortFiles($files);
        // if (count($subchapter_files) < 1)
        // continue;
        $subchapters = [];
        foreach ($subchapter_files as $subchapter_file) {
            $subchapter_file_path = $chapter_file_path . "/" . $subchapter_file;
            if (is_dir($subchapter_file_path)) {
                if (isFolderHidden($subchapter_file_path))
                    continue;
                $subchapters = array_merge($subchapters, array(
                    mb_strtoupper(getFolderBasename($subchapter_file)) => array(
                        "path" => $subchapter_file
                    )
                ));
            }
        }

        $chapters_and_subchapters = array_merge($chapters_and_subchapters, array(
            mb_strtoupper(getFolderBasename($chapter_path)) => [
                "chapter_path" => $chapter_path,
                "subchapters" => $subchapters
            ]
        ));
    }
}

if (isset($_GET['chapter']) && key_exists(mb_strtoupper($_GET['chapter']), $chapters_and_subchapters)) {
    $chapter_name = mb_strtoupper($_GET['chapter']);
    echo "<script>var CURRENT_CHAPTER = '$chapter_name';</script>";
    if (isset($_GET['subchapter']) && key_exists(mb_strtoupper($_GET['subchapter']), $chapters_and_subchapters[$chapter_name]["subchapters"])) {
        $subchapter_name = mb_strtoupper($_GET['subchapter']);
        echo "<script>var CURRENT_SUBCHAPTER = '$subchapter_name';</script>";
    } else
        echo "<script>var CURRENT_SUBCHAPTER = null;</script>";
} else {
    echo "<script>var CURRENT_CHAPTER = null;</script>";
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PALKHHHHHHHHHHHHH</title>

    <!-- Иконка -->
    <link rel="icon" href="./images/icon.jpg" type="image/x-icon">
    <link rel="shortcut icon" href="./images/icon.jpg" type="image/x-icon">

    <!-- Подключение внешнего файла -->
    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="./js/cursorPaint.css">
</head>

<body class="overflow-y">

    <canvas id="canvasPaint" class="canvas-paint"></canvas>

    <div class="z-2" style="position:absolute; width: 35%;">

        <div class="d-flex flex-row justify-content-end mt-1">
            <h4 class="title">ПАЛЬХ</h4>
        </div>

        <div class="d-flex flex-row justify-content-end mb-2 mt-1">

            <?php
            foreach ($chapters_and_subchapters as $key => $chapter) {
            ?>
                <div class="form_radio_btn ms-1" onclick="clickToChapter('<?= $key ?>')">
                    <input id="input-radio-<?= $key ?>" type="radio" name="input-radio" value="<?= $key ?>">
                    <label for="input-radio-<?= $key ?>"><?= $key ?></label>
                </div>
            <?php
            } ?>

        </div>

        <div id="div-chapters-description">
            <?php
            foreach ($chapters_and_subchapters as $key => $chapter) { ?>
                <div id="div-<?= $key ?>" class="d-flex flex-column d-none mt-1">
                    <?php
                    foreach ($chapter['subchapters'] as $key => $subchapter) { ?>
                        <div class="d-flex flex-row justify-content-end">
                            <button id="button-subchapter-<?= $key ?>" class="link text-secondary mb-1" onclick="clickToSubchapter('<?= $key ?>', this)">
                                <?= $key ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

    </div>

    <div id="div-view" class="z-0" style="position:absolute; width: 64%; margin-left: 36%">

        <div id="div-view-chapter" class="d-flex flex-column"></div>
        <div id="div-view-subchapter" class="d-flex flex-column"></div>

    </div>


    <!-- <div class="d-flex flex-row w-100" style="position:absolute;">

        <div class="w-50 me-2"></div>

        <div id="div-view" class="w-100 z-0">

            <div id="div-view-chapter" class="d-flex flex-column"></div>
            <div id="div-view-subchapter" class="d-flex flex-column"></div>

        </div>
    </div> -->

    </div>

</body>

<script src="/src/jquery-3.7.1.min.js"></script>
<script src="./js/cursorPaint.js"></script>
<script type="text/javascript">
    const CHAPTERS_AND_SUBCHAPTERS = <?= json_encode($chapters_and_subchapters) ?>;

    $(document).ready(function() {
        if (CURRENT_CHAPTER != null) {
            let chapter_name = CURRENT_CHAPTER;
            CURRENT_CHAPTER = null;
            document.getElementById("input-radio-" + chapter_name).click();

            if (CURRENT_SUBCHAPTER != null) {
                let subchapter_name = CURRENT_SUBCHAPTER;
                CURRENT_SUBCHAPTER = null;
                document.getElementById("button-subchapter-" + subchapter_name).click();
                document.getElementById("button-subchapter-" + subchapter_name).focus();
            }
        }
    });

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

        if (CURRENT_CHAPTER == chapter_name)
            return;

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
        Object.entries(CHAPTERS_AND_SUBCHAPTERS[CURRENT_CHAPTER]['subchapters']).forEach(([key, value]) => {
            if (key == subchapter_name) {
                flag = true;
                return;
            }
        });
        return flag;
    }

    async function clickToSubchapter(subchapter_name, clicked_elem) {

        if (CURRENT_CHAPTER == null || !isCorrectChapter(CURRENT_CHAPTER))
            return;
        chapter_name = CURRENT_CHAPTER;

        if (!isCorrectSubchapter(subchapter_name))
            return;

        let ajaxResponse = await ajaxGetSubChapterHtml(chapter_name, subchapter_name);
        if (ajaxResponse == null) {
            return;
        }

        document.querySelectorAll(".link").forEach(function(element) {
            element.classList.remove("link-active");
        });
        clicked_elem.classList.add("link-active");
        document.getElementById("div-view-subchapter").innerHTML = ajaxResponse;
        scrollTop();
    }

    function scrollTop() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    async function ajaxGetSubChapterHtml(chapter_name, subchapter_name = null) {

        var formData = new FormData();
        formData.append('chapter_name', CHAPTERS_AND_SUBCHAPTERS[chapter_name]['chapter_path']);
        if (subchapter_name != null)
            formData.append('subchapter_name', CHAPTERS_AND_SUBCHAPTERS[chapter_name]['subchapters'][subchapter_name]['path']);

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
            // console.log(response.trim());
            response = response.trim();
        } catch (error) {
            console.error('Ошибка запроса:', error);
            return null;
        }

        return response;
    }
</script>

</html>