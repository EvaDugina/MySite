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
    <link
        rel="shortcut icon"
        href="./images/ОПЛОДОТВОРЕНИЕ_LOD2.jpg"
        type="image/x-icon" />

    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/flash.css" />
    <link rel="stylesheet" href="./css/cursor.css" />
</head>

<body>
    <img
        id="img-cursor"
        class="cursor not-allowed z-999"
        src="./images/cursors/none.png"
        alt="icon" />
    <div id="div-inviolable" class="background z-6 d-none"></div>
    <div id="div-photo" class="portrait-container">

        <div id="cursors-container">
        </div>

        <button id="btn-click" class="not-allowed z-5">
            неприкосновенна
        </button>

        <div id="div-point" class="point z-4"></div>

        <div id="div-flash-4" class="flash-container z-3 d-none">
            <img
                id="img-flash-4"
                class="flash not-allowed"
                src="./images/УКРАЛИ.jpg"
                alt="УКРАЛИ" />
        </div>
        <div id="div-flash-3" class="flash-container z-3 d-none">
            <img
                id="img-flash-3"
                class="flash not-allowed"
                src="./images/ОПЛОДОТВОРЕНИЕ.jpg"
                alt="ОПЛОДОТВОРЕНИЕ" />
        </div>
        <div id="div-flash-2" class="flash-container z-3 d-none">
            <img
                id="img-flash-2"
                class="flash not-allowed"
                src="./images/04.jpg"
                alt="ВСПЫШКА" />
        </div>
        <div id="div-flash-1" class="flash-container z-3 d-none">
            <img
                id="img-flash-1"
                class="flash not-allowed"
                src="./images/01_2.jpg"
                alt="ВСПЫШКА" />
        </div>
        <div id="div-flash-0" class="flash-container z-3 d-none">
            <div id="div-flash-0-back" class="flash-container"></div>
        </div>

        <video
            id="video-portrait"
            class="portrait not-allowed z-2 d-none"
            poster="./images/НЕПРИКОСНОВЕННА.png"
            preload="auto"
            muted>
            <source src="./videos/ЛИЗА ПЛАЧЕТ.webm" type="video/webm" />
            <source src="./videos/ЛИЗА ПЛАЧЕТ.mp4" type="video/mp4" />
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

    #div-back {
        opacity: 1;
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
<script src="./js/deviceHandler.js"></script>
<script src="./js/cursorHandler.js"></script>
<script src="./js/portraitHandler.js"></script>
<script src="./js/flashHandler.js"></script>
<script src="./js/randomHandler.js"></script>
<!-- <script src="./js/links/index.js"></script> -->
<script type="text/javascript">
    const $CURSORS_CONTAINER = $("#cursors-container");
    const $PORTRAIT = $("#img-portrait");
    const $BUTTON = $("#btn-click");
    const $POINT = $("#div-point");
    const $INVOIOLABLE = $("#div-inviolable");
    const $BACKGROUND = $("#div-back");

    const VISITORS_DATA = <?php echo json_encode($visitorsJSON); ?>;
    const UUID = '<?php echo $uuid; ?>';

    $("body").hide();
    $(document).ready(function() {
        $("body").show();
    });

    $(window).on("load", function() {
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
        startX: 0.9, // Начальная позиция от width по X
        startY: 0.25, // Начальная позиция от рушпре по Y
        handleLeftClickDown: handleLeftClickDown,
        handleLeftClickUp: handleLeftClickUp,
        handleDoubleLeftClick: null,
        stiffness: 0.4, // Жесткость пружины (скорость реакции)
        damping: 0.1, // Затухание (плавность остановки)
        mass: 0.1, // Масса объекта
        maxSpeed: 0.5, // Максимальная скорость
        // maxSpeed: 10, // Максимальная скорость (для отладки)
    };

    const Zone = {
        NONE: 0,
        PHOTO: 1,
        BUTTON: 2,
        POINT: 3,
        INVOIOLABLE: 4,
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
            element: $PORTRAIT,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: null,
            handleOff: null,
        },
        [Zone.BUTTON]: {
            element: $BUTTON,
            imgCursor: CURSOR_IMAGES.POINTER,
            imgCursorClicked: CURSOR_IMAGES.POINTER_CLICKED,
            handleOn: null,
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

    function cursorOnPoint() {
        $BACKGROUND.removeClass("bg-blue");
        $BUTTON.addClass("hovered");
    }

    function cursorOffPoint() {
        clearInterval(intervalId);
        $BACKGROUND.addClass("bg-blue");
        $BUTTON.removeClass("hovered");
    }

    //
    // CURSOR CLICK CONTROLL
    //

    const LIMIT_CLICK = 3;
    var countClick = 0;
    var intervalId = null;
    var IS_VULNERABLE = false;

    async function handleLeftClickDown(event) {

        updateLastClickPosition(event.clientX, event.clientY)

        if (isCursorZone(Zone.POINT)) {
            if (!IS_VULNERABLE) {
                if (isSensoreDevice()) {
                    countClick += 1;
                    if (countClick > LIMIT_CLICK) {
                        qwerty();
                    }
                } else {
                    clearInterval(intervalId);
                    intervalId = setInterval(
                        handleLeftClickDownDuration,
                        100,
                    );
                }
            }
            startFlashes();
            return;
        } else if (isCursorZone(Zone.BUTTON)) {
            clickButton();
        }

        return;
    }

    async function handleLeftClickUp(event) {
        clearInterval(intervalId);
    }

    async function handleLeftClickDownDuration(event) {
        let clickDuration = Date.now() - clickStartTime;
        if (
            isCursorZone(Zone.POINT) &&
            clickDuration > 500 &&
            !IS_VULNERABLE
        ) {
            qwerty();
        }
    }

    async function qwerty() {
        IS_VULNERABLE = true;
        clearInterval(intervalId);
        stopCursor();
        await clickPoint();
    }

    async function clickPoint() {
        $BUTTON.css("pointer-events", "none");

        showVideo();

        $BUTTON.removeClass("hovered");
        $BUTTON.removeClass("active");
        $BUTTON.attr("disabled", true);

        startVideo();
    }

    async function clickButton() {
        $BUTTON.addClass("active");
        setTimeout(() => {
            $BUTTON.removeClass("active");
        }, 300);
    }

    async function startFlashes() {
        let number = getRandomInt(1, 2);
        await flash([...generateFlashArray(number)]);
    }

    function startVideo() {
        playVideo();

        setTimeout(() => {
            changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursor"]);
        }, 3.75 * 1000);

        setTimeout(() => {
            $BUTTON.attr("disabled", false);
        }, 22 * 1000);

        setTimeout(() => {
            $CURSORS_CONTAINER.addClass("d-none");
            disableCursor();
            changeCursorSrc(null);
        }, 28 * 1000);
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
    };
</script>
<script src="./js/cursorController.js"></script>
<script src="./js/videoController.js"></script>

</html>