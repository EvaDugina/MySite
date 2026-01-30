<?php
// require_once __DIR__ . "/server/fileHandler.php";
// $dataFileName = __DIR__ . '/server/data/visitors.json';
// $visitorsJSON = json_decode(readFileSafe($dataFileName), true);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Неприкосновенна</title>

    <link
        rel="icon"
        href="./images/ОПЛОДОТВОРЕНИЕ_LOD2.jpg"
        type="image/x-icon" />
    <link
        rel="shortcut icon"
        href="./images/ОПЛОДОТВОРЕНИЕ_LOD2.jpg"
        type="image/x-icon" />

    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/cursor.css" />
</head>

<body>
    <img
        id="img-cursor"
        class="cursor not-allowed z-999"
        src="./images/cursors/none.png"
        alt="icon" />
    <div id="div-photo" class="portrait-container">
        <div id="cursors-container">
        </div>
        <video
            id="video-portrait"
            class="portrait not-allowed z-2"
            poster="./images/НЕПРИКОСНОВЕННА.png"
            preload="auto"
            muted>
            <source src="./videos/ЛИЗА ПЛАЧЕТ.webm" type="video/webm" />
            <source src="./videos/ЛИЗА ПЛАЧЕТ.mp4" type="video/mp4" />
        </video>
    </div>
</body>

<style>
    #div-photo {
        width: 40%;
    }

    @media (min-width: 768px) {
        #div-photo {
            width: 35%;
        }
    }

    @media (min-width: 1024px) {
        #div-photo {
            width: 30%;
        }
    }

    @media (min-width: 1280px) {
        #div-photo {
            width: 25%;
        }
    }

    @media (min-width: 1440px) {
        #div-photo {
            width: 25%;
        }
    }

    @media (min-width: 1600px) {
        #div-photo {
            width: 20%;
        }
    }

    @media (min-width: 1920px) {
        #div-photo {
            width: 20%;
        }
    }
</style>

<script src="/src/jquery-3.7.1.min.js"></script>
<script src="./js/preloadController.js"></script>
<script src="./js/cursorHandler.js"></script>
<script type="text/javascript">
    const $IMG_PORTRAIT = $("#video-portrait");

    function getPortraitMetrics() {
        return {
            "leftX": $IMG_PORTRAIT.offset().left,
            "topY": $IMG_PORTRAIT.offset().top,
            "width": $IMG_PORTRAIT.outerWidth(), // Use .outerWidth() to include padding/border
            "height": $IMG_PORTRAIT.outerHeight() // Use .outerHeight() to include padding/border
        }
    }

    // 
    // CURSOR ELSE CONTROLL
    // 

    var visitorsData = <?php echo json_encode($visitorsJSON); ?>;
    var $cursorElseElements = []

    function createCursorElse(positionX, positionY) {
        let $element = $('<img>', {
            class: 'cursor cursor-else not-allowed z-998',
            css: {
                position: 'absolute',
                left: positionX + '%',
                top: positionY + '%'
            },
            src: './images/cursors/pointer.png',
            alt: 'муха'
        }).appendTo('#cursors-container'); // или к нужному контейнеру

        $cursorElseElements.push($element)
    }

    // Функция для смены изображений
    function clickCursorsElse() {

        $cursorElseElements.forEach(($this, index) => {
            setTimeout(async () => {
                changeCursorSrc(CURSOR_IMAGES.POINTER_CLICKED, $this)
                setTimeout(async () => {
                    changeCursorSrc(CURSOR_IMAGES.POINTER, $this)
                }, 50);
            }, getRandomInt(0, 4) * 100);
        });
    }

    $(document).ready(function() {

        if (typeof visitorsData !== 'undefined') {
            $.each(visitorsData, function(key, visitor) {
                createCursorElse(visitor.positionX, visitor.positionY)
            });
        }

        setInterval(clickCursorsElse, 500);

        playVideo()
    });

    //
    // CURSOR MOVE CONTROLL
    //

    // Настройки cursorController
    const SETTINGS = {
        elementCursor: $("#img-cursor"), // Объект курсора
        timeout: 0, // Задержка перед началом
        startX: 0.75, // Начальная позиция от width по X
        startY: 0.25, // Начальная позиция от рушпре по Y
        handleDbLeftClick: null,
        stiffness: 1, // Жесткость пружины (скорость реакции)
        damping: 0.1, // Затухание (плавность остановки)
        mass: 0.1, // Масса объекта
        maxSpeed: 50, // Максимальная скорость
    };

    const Zone = {
        NONE: 0,
        PHOTO: 1,
    };

    const ZONES_SETTINGS = {
        [Zone.NONE]: {
            element: null,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: null,
            handleOff: null,
        },
        [Zone.PHOTO]: {
            element: $IMG_PORTRAIT,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: null,
            handleOff: null,
        },
    };

    //
    // CURSOR CLICK CONTROLL
    //

    async function handleLeftClick(event) {
        changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursorClicked"]);
        // setTimeout(() => {
        //     changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursor"]);
        // }, 200);

        let portraitMetrics = getPortraitMetrics()

        let positionX = event.clientX - portraitMetrics['leftX']
        let positionY = event.clientY - portraitMetrics['topY']

        let percentX = ((positionX / portraitMetrics['width']) * 100).toFixed(2)
        let percentY = ((positionY / portraitMetrics['height']) * 100).toFixed(2)

        createCursorElse(percentX, percentY)

        return;
    }

    function fixCursorPosition() {}

    //
    // VIDEO CONTROLL
    //

    const VIDEO_SETTINGS = {
        $element: $("#video-portrait"),
    };
</script>
<script src="./js/cursorController.js"></script>
<script src="./js/videoController.js"></script>
<script src="./js/randomHandler.js"></script>

</html>