const IS_SENSORE_DEVICE = isSensoreDevice()

var targetX = 0
var targetY = 0
var currentX = 0
var currentY = 0
var velocityX = 0
var velocityY = 0

var CurrentZone = Zone.NONE
var currentCusorSrc = null

var IS_STOPPED = false
var IS_CLICKED = false
var IS_FREEZED_CURSOR_SRC = false

var dbClickCount = 0
var dbClickTimeout

var animationId = null
var RectZones

var clickStartTime = 0

//
// PUBLIC FUNCTIONS
//

function isCursorZone(zoneType) {
    return CurrentZone == zoneType
}

function getCursorPosition() {
    return {
        x: currentX,
        y: currentY,
    }
}

function getCursorSrc() {
    return currentCusorSrc
}

function freezeCursorSrc(src) {
    IS_FREEZED_CURSOR_SRC = false
    changeCursorSrc(src)
    IS_FREEZED_CURSOR_SRC = true
}

function hideCursor() {
    freezeCursorSrc(null)
}

function disableCursor() {
    window.removeEventListener("mousedown", onMouseDown)
    window.removeEventListener("mouseup", onMouseUp)
    window.removeEventListener("touchstart", onMouseDown)
    window.removeEventListener("touchend", onMouseUp)
}

function stopCursor() {
    window.removeEventListener("mousemove", handleMosemove)
    window.removeEventListener("blur", handleBlur)
    IS_STOPPED = true
}

function restartCursor() {
    initRectZones()
    start(true)
    handleBlur()
    updateCurrentZone()
    IS_STOPPED = false
}

function initCursorController() {
    initRectZones()
    initPosition()
    start()

    window.onresize = function () {
        window.location.reload()
    }

    window.addEventListener("mousedown", onMouseDown)
    window.addEventListener("touchstart", onMouseDown)
    window.addEventListener("mouseup", onMouseUp)
    window.addEventListener("touchend", onMouseUp)
}

//
// LOCAL FUNCTIONS
//

async function onMouseDown(event) {
    if (event.which === 1) {
        if (IS_CLICKED) return
        IS_CLICKED = true
        changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursorClicked"])

        dbClickCount += 1
        clickStartTime = Date.now()

        if (dbClickCount === 1) {
            // Первый клик - ждем второй
            dbClickTimeout = setTimeout(() => {
                // Если не было второго клика за 300мс
                dbClickCount = 0
            }, 300)
            if (SETTINGS.handleLeftClickDown !== null)
                await SETTINGS.handleLeftClickDown(event)
        } else if (dbClickCount === 2) {
            // Второй клик - это двойной клик
            clearTimeout(dbClickTimeout)
            if (SETTINGS.handleDoubleLeftClick !== null)
                SETTINGS.handleDoubleLeftClick(event)
            dbClickCount = 0
        }

        IS_CLICKED = false
    }
}

async function onMouseUp(event) {
    if (event.which === 1) {
        let timeout = 0
        if (IS_SENSORE_DEVICE) timeout = 200
        setTimeout(async () => {
            if (SETTINGS.handleLeftClickUp !== null)
                await SETTINGS.handleLeftClickUp(event)
            changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursor"])
        }, timeout)
    }
}

//
// INIT FUNCTIONS
//

// Инициализация Rect Zones
function initRectZones() {
    RectZones = new Map()
    Object.entries(Zone).forEach(([key, value]) => {
        if (ZONES_SETTINGS[value]["element"] !== null)
            RectZones.set(
                value,
                ZONES_SETTINGS[value]["element"][0].getBoundingClientRect(),
            )
    })
}

// Инициализация начальной позиции
function initPosition() {
    if (SETTINGS.startX !== null && SETTINGS.startY !== null) {
        currentX = targetX = window.innerWidth * SETTINGS.startX
        currentY = targetY = window.innerHeight * SETTINGS.startY

        // Устанавливаем начальную позицию
        SETTINGS.elementCursor.css({
            left: currentX + "px",
            top: currentY + "px",
        })
    } else {
        currentX = null
        currentY = null
    }
}

function start(isRestart = false) {
    // Запускаем анимацию
    if (!animationId) {
        animationId = requestAnimationFrame(updatePosition)
    }

    let timeout = SETTINGS.timeout
    if (isRestart) timeout = 0

    setTimeout(() => {
        window.addEventListener("mousemove", handleMosemove)
        window.addEventListener("blur", handleBlur)
    }, timeout * 1000)
}

//
//
//

function handleMosemove(e) {
    // Обновляем целевую позицию (центрируем элемент на курсоре)
    targetX = e.clientX
    targetY = e.clientY

    if (currentX === null || currentY === null) {
        currentX = targetX
        currentY = targetY
    }

    // Запускаем анимацию, если она еще не запущена
    if (!animationId) {
        animationId = requestAnimationFrame(updatePosition)
    }
}

function handleBlur() {
    if (animationId) {
        // cancelAnimationFrame(animationId)
        animationId = null
    }
}

//
//
//

// Функция для плавного обновления позиции
function updatePosition() {
    if (IS_STOPPED) return
    if (
        (currentX === null || currentY === null) &&
        targetX === 0 &&
        targetY === 0
    ) {
        animationId = requestAnimationFrame(updatePosition)
        return
    }

    // Рассчитываем силу (разница между текущей и целевой позицией)
    const forceX = (targetX - currentX) * SETTINGS.stiffness
    const forceY = (targetY - currentY) * SETTINGS.stiffness

    // Ускорение = сила / масса
    const accelerationX = forceX / SETTINGS.mass
    const accelerationY = forceY / SETTINGS.mass

    // Обновляем скорость с учетом ускорения и затухания
    velocityX = (velocityX + accelerationX) * SETTINGS.damping
    velocityY = (velocityY + accelerationY) * SETTINGS.damping

    // Ограничиваем максимальную скорость
    const speed = Math.sqrt(velocityX * velocityX + velocityY * velocityY)
    if (speed > SETTINGS.maxSpeed) {
        velocityX = (velocityX / speed) * SETTINGS.maxSpeed
        velocityY = (velocityY / speed) * SETTINGS.maxSpeed
    }

    // Обновляем позицию
    currentX += velocityX
    currentY += velocityY

    // Применяем к элементу
    SETTINGS.elementCursor.css({
        left: currentX + "px",
        top: currentY + "px",
    })

    updateCurrentZone()

    // Продолжаем анимацию
    animationId = requestAnimationFrame(updatePosition)
}

function updateCurrentZone() {
    let foundZone = false

    // Получаем все зоны и переворачиваем порядок для обратной проверки
    const zoneEntries = Object.entries(Zone)

    // Идем в обратном порядке (от последней зоны к первой)
    for (let i = zoneEntries.length - 1; i >= 0; i--) {
        const [key, value] = zoneEntries[i]
        if (isCursorInZone(value)) {
            foundZone = true

            // Вход в новую зону
            if (CurrentZone !== value) {
                // Выход из предыдущей зоны
                handleOffCurrentZone()

                // Вход в новую зону
                CurrentZone = value
                changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursor"])
                handleOnCurrentZone()
            }

            break
        }
    }

    // Если курсор не в активной зоне, выходим из нее
    if (!foundZone) {
        handleOffCurrentZone()

        CurrentZone = Zone.NONE
        changeCursorSrc(ZONES_SETTINGS[CurrentZone]["imgCursor"])
        handleOnCurrentZone()
    }
}

function isCursorInZone(zoneType) {
    if (zoneType == Zone.NONE) return true
    if (currentX === null || currentY === null) return false

    let radius = 0
    let rect = RectZones.get(zoneType)

    return (
        currentX + radius >= rect.left &&
        currentX - radius <= rect.right &&
        currentY + radius >= rect.top &&
        currentY - radius <= rect.bottom
    )
}

function handleOnCurrentZone() {
    if (ZONES_SETTINGS[CurrentZone]["handleOn"] === null) return
    ZONES_SETTINGS[CurrentZone]["handleOn"]()
}

function handleOffCurrentZone() {
    if (ZONES_SETTINGS[CurrentZone]["handleOff"] === null) return
    ZONES_SETTINGS[CurrentZone]["handleOff"]()
}

//
//
//

async function changeCursorSrc(newSrc, cursorElement = null) {
    if (IS_FREEZED_CURSOR_SRC) return

    if (cursorElement === null) cursorElement = SETTINGS.elementCursor

    if (newSrc === cursorElement.attr("src")) return
    else if (newSrc === null) newSrc = CURSOR_IMAGES.NONE

    let durationAnimation = 0

    // Fade the image out
    cursorElement.fadeOut(durationAnimation, function () {
        // Change the src attribute after the fade out is complete
        cursorElement.attr("src", newSrc)
        currentCusorSrc = newSrc

        // Wait for the new image to load before fading it in
        // Use .one('load', ...) to ensure the callback runs only once
        cursorElement
            .one("load", function () {
                $(this).fadeIn(durationAnimation)
            })
            .each(function () {
                // Handle cached images that might not trigger the load event
                if (this.complete) $(this).trigger("load")
            })
    })
}
