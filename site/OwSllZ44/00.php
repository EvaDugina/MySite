<?php
require_once __DIR__ . "/server/visitorHandler.php";
$visitorsJSON = getVisitorPositions();
$uuid = createOrGenerateUUID();
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

    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/cursor.css" />
    <link rel="stylesheet" href="./css/flash.css" />
</head>

<body>
    <img
        id="img-cursor"
        class="cursor not-allowed z-999"
        src="./images/cursors/none.png"
        alt="icon"
        crossorigin="anonymous" />
    <div id="div-inviolable" class="background z-7 d-none"></div>
    <div id="div-photo" class="portrait-container">

        <div id="cursors-container">
        </div>

        <button id="btn-click" class="not-allowed z-6">
            неприкосновенна
        </button>

        <div id="div-point" class="point z-5"></div>

        <div id="div-flash-2" class="flash-container z-3 d-none">
            <img
                id="img-flash-2"
                class="flash not-allowed"
                src="./images/04.jpg"
                alt="ВСПЫШКА"
                crossorigin="anonymous" />
        </div>
        <div id="div-flash-1" class="flash-container z-3 d-none">
            <img
                id="img-flash-1"
                class="flash not-allowed"
                src="./images/01_2.jpg"
                alt="ВСПЫШКА"
                crossorigin="anonymous" />
        </div>
        <div id="div-flash-0" class="flash-container z-3 d-none">
            <div id="div-flash-0-back" class="flash-container"></div>
        </div>

        <video
            id="video-portrait"
            class="portrait not-allowed z-2 d-none"
            poster="./images/НЕПРИКОСНОВЕННА.png"
            preload="auto"
            muted
            crossorigin="anonymous">
            <source src="./videos/ЛИЗА ПЛАЧЕТ (22 секунды).webm" type="video/webm" />
            <source src="./videos/ЛИЗА ПЛАЧЕТ (22 секунды).mp4" type="video/mp4" />
            <!-- <source src="./videos/ЛИЗА ПЛАЧЕТ.webm" type="video/webm" />
            <source src="./videos/ЛИЗА ПЛАЧЕТ.mp4" type="video/mp4" /> -->
        </video>

        <img
            id="img-portrait"
            class="portrait not-allowed z-1"
            src="./images/НЕПРИКОСНОВЕННА.png"
            alt="НЕПРИКОСНОВЕННА" />
    </div>

    <div id="div-back" class="background bg-blue z-3"></div>
</body>

<style>
    #div-flash-0 {
        mix-blend-mode: exclusion;
    }

    #div-flash-0-back {
        background-color: lemonchiffon;
        width: 99.5%;
        height: 98.5%;
        margin: 0.2%;
    }

    #img-flash-1 {
        height: 112%;
        top: -4.5%;
        left: -52%;
    }

    #img-flash-2 {
        height: 228%;
        top: -29%;
        left: -64%;
    }

    #img-flash-3 {
        height: 116%;
        top: -5%;
        left: -88%;
    }

    #img-flash-4 {
        height: 141%;
        top: 50%;
        left: 52%;
        align-self: center;
        transform: translate(-50%, -50%);
    }

    /*


        */

    #div-photo {
        width: 75%;
    }

    #video-portrait {
        position: absolute;
        transition: opacity 0.8s ease-in-out;
    }

    #btn-click {
        position: absolute;
        transform: translate(-50%, -50%);
        top: 75%;
        left: 50%;
        font-size: var(--font-size-sm);
    }

    #div-point {
        position: absolute;
        top: 75.4%;
        left: 54.5%;
        width: 3px;
        height: 3px;
        display: block;
        margin: 0;
        align-self: center;
        background-color: red;
        opacity: 0;
        transform: translate(-50%, -50%);
    }

    @media (min-width: 768px) {
        #div-photo {
            width: 50%;
        }
    }

    @media (min-width: 1024px) {
        #div-photo {
            width: 40%;
        }
    }

    @media (min-width: 1280px) {
        #div-photo {
            width: 30%;
        }
    }

    @media (min-width: 1440px) {
        #div-photo {
            width: 25%;
        }
    }

    @media (min-width: 1600px) {
        #div-photo {
            width: 25%;
        }
    }

    @media (min-width: 1920px) {
        #div-photo {
            width: 20%;
        }
    }
</style>

<script src="/src/jquery-3.7.1.min.js"></script>
<script src="/src/html2canvas.min.js"></script>

<script src="./js/links/00.js"></script>
<script src="/js/screenshotHandler.js"></script>
<script src="./js/deviceHandler.js"></script>
<script src="./js/cursorHandler.js"></script>
<script src="./js/portraitHandler.js"></script>
<script src="./js/flashHandler.js"></script>
<script src="./js/randomHandler.js"></script>
<script type="text/javascript">
    const $BODY = $("body");
    $BODY.hide();
    $(document).ready(function() {
        $BODY.show();
    });

    const $CURSORS_CONTAINER = $("#cursors-container");
    const $PORTRAIT = $("#img-portrait");
    const $BUTTON = $("#btn-click");
    const $POINT = $("#div-point");
    const $INVOIOLABLE = $("#div-inviolable");
    const $BACKGROUND = $("#div-back");

    const VISITORS_DATA = <?php echo json_encode($visitorsJSON); ?>;
    const UUID = '<?php echo $uuid; ?>';

    $(window).on("load", function() {

        showVideo();

        if (typeof VISITORS_DATA !== 'undefined') {
            $.each(VISITORS_DATA, function(key, visitor) {
                if (visitor.positionX == null || visitor.positionY == null)
                    return
                if (key == UUID) {
                    let portraitMetrics = getPortraitMetrics($PORTRAIT)
                    let startX = (visitor.positionX / 100 * portraitMetrics['width'] + portraitMetrics['leftX']) / window.innerWidth
                    let startY = (visitor.positionY / 100 * portraitMetrics['height'] + portraitMetrics['topY']) / window.innerHeight
                    if (startX < 1 && startY < 1) {
                        SETTINGS.startX = startX
                        SETTINGS.startY = startY
                    }
                    return
                }
                createCursorElse($CURSORS_CONTAINER, visitor.positionX, visitor.positionY)
            });
        }
        setInterval(clickCursorsElse, 500);

        initCursorController()
    });

    //
    // CURSOR MOVE CONTROLL
    //

    // Настройки cursorController
    const SETTINGS = {
        elementCursor: $("#img-cursor"), // Объект курсора
        timeout: 0, // Задержка перед началом
        startX: null, // Начальная позиция от width по X
        startY: null, // Начальная позиция от рушпре по Y
        handleLeftClickDown: handleLeftClickDown,
        handleLeftClickUp: handleLeftClickUp,
        handleDoubleLeftClick: null,
        stiffness: 0.4, // Жесткость пружины (скорость реакции)
        damping: 0.1, // Затухание (плавность остановки)
        mass: 0.1, // Масса объекта
        maxSpeed: 1, // Максимальная скорость
        // maxSpeed: 10, // Максимальная скорость (для отладки)
    };

    const Zone = {
        NONE: 0,
        BACK: 1,
        PORTRAIT: 2,
        BUTTON: 3,
        POINT: 4,
        INVOIOLABLE: 5,
    };

    const ZONES_SETTINGS = {
        [Zone.NONE]: {
            element: null,
            imgCursor: CURSOR_IMAGES.NONE,
            imgCursorClicked: CURSOR_IMAGES.NONE,
            handleOn: null,
            handleOff: null,
        },
        [Zone.BACK]: {
            element: $BODY,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: null,
            handleOff: null,
        },
        [Zone.PORTRAIT]: {
            element: $PORTRAIT,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: cursorOnPortrait,
            handleOff: null,
        },
        [Zone.BUTTON]: {
            element: $BUTTON,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: cursorOnButton,
            handleOff: null,
        },
        [Zone.POINT]: {
            element: $POINT,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: cursorOnPoint,
            handleOff: cursorOffPoint,
        },
        [Zone.INVOIOLABLE]: {
            element: $INVOIOLABLE,
            imgCursor: null,
            imgCursorClicked: null,
            handleOn: null,
            handleOff: null,
        },
    };

    function cursorOnPortrait() {
        $BUTTON.removeClass("hovered");
        unclickButton()
    }

    function cursorOnButton() {
        $BUTTON.addClass("hovered");
    }

    function cursorOnPoint() {
        $BACKGROUND.removeClass("bg-blue");
        $BUTTON.addClass("hovered");
    }

    function cursorOffPoint() {
        $BACKGROUND.addClass("bg-blue");
        $BUTTON.removeClass("hovered");
        unclickPoint()
    }

    //
    // CURSOR CLICK CONTROLL
    //

    var IS_POINT_CLICKED = false;
    var IS_BOLNO = false

    var intervalId = null

    async function handleLeftClickDown(event) {

        if (isCursorZone(Zone.POINT)) {
            if (!IS_POINT_CLICKED) {
                // stopCursor()
                intervalId = setInterval(
                    handleVideoDuration,
                    100,
                );
                IS_POINT_CLICKED = true;
            }
            clickPoint();
            return;
        } else if (isCursorZone(Zone.BUTTON)) {
            clickButton();
        } else {

        }

        updateLastClickPosition(event.clientX, event.clientY)

        return;
    }

    async function handleLeftClickUp(event) {
        if (isCursorZone(Zone.POINT)) {
            unclickPoint()
        } else if (isCursorZone(Zone.BUTTON)) {
            unclickButton()
        }
    }

    async function handleVideoDuration() {
        if (getVideoCurrentTime() > 6) {
            clearInterval(intervalId);
            if (!isVideoPlaying())
                playVideo()
            IS_BOLNO = true
            $BACKGROUND.addClass("d-none");
            // $BUTTON.addClass("hovered");
            // $BUTTON.attr("disabled", false);
            // startFlashes()
        }
    }

    async function clickPoint() {
        startFlashes();
        $CURSORS_CONTAINER.addClass("d-none");
        $BUTTON.removeClass("hovered");
        $BUTTON.attr("disabled", true);
        if (IS_BOLNO) return
        playVideo();
    }

    async function unclickPoint() {
        $CURSORS_CONTAINER.removeClass("d-none");
        $BUTTON.addClass("hovered");
        $BUTTON.attr("disabled", false);
        if (IS_BOLNO) return
        pauseVideo();
    }

    async function clickButton() {
        $BUTTON.addClass("active");
    }

    async function unclickButton() {
        $BUTTON.removeClass("active");
    }

    async function startFlashes(n = 1) {
        for (let i = 0; i < n; i++) {
            let number = getRandomInt(1, 2);
            await flash([...generateFlashArray(number)]);
        }
        setTimeout(() => {
            makeScreenshot()
        }, 500);
    }

    // 
    // CURSOR ELSE CONTROLL
    // 

    function updateLastClickPosition(clientX, clientY) {

        let cursorPosition = getCursorPosition()
        let percents = getCursorPositionRelativePortrait(
            cursorPosition.x,
            cursorPosition.y,
            $PORTRAIT,
        );

        ajaxSaveLastCursorPosition(UUID, percents.x, percents.y)
    }

    //
    // FLASH CONTROLL
    //

    const FLASH_SETTINGS = {
        duration: 150,
    };

    function generateFlashArray(number) {
        return [number, 0, number, 0, number, number, number];
    }

    //
    // VIDEO CONTROLL
    //

    const VIDEO_SETTINGS = {
        $element: $("#video-portrait"),
        onEnded: handleVideoEnded
    };

    async function handleVideoEnded(event) {
        disableCursor()
        stopCursor()
        startFlashes();
        $BUTTON.attr("disabled", false);
        $BUTTON.removeClass("hovered");
        setTimeout(() => {
            hideCursor()
            $CURSORS_CONTAINER.removeClass("d-none");
            startFlashes(3);
        }, 1.5 * 1000);
    }

    // 
    // SCREENSHOT CONTROLL
    // 

    const SCREENSHOT_SETTINGS = {
        screenshot_name: "Неприкосновенна",
        $canvas: null,
    };

    async function makeScreenshot() {
        await captureScreenshot(false);
    }
</script>
<script src="./js/cursorController.js"></script>
<script src="./js/videoController.js"></script>

</html>