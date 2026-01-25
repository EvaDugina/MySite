var targetX = 0
var targetY = 0
var currentX = 0
var currentY = 0
var velocityX = 0
var velocityY = 0

var CurrentZone = Zone.NONE;

var IS_STOPPED = false;
var animationId = null
var RectZones;

// 
// 
// 

function isStoped() {
    return IS_STOPPED
}

function isCursorZone(zoneType) {
    return CurrentZone == zoneType
}

// 
// 
// 

window.onresize = function () {
    window.location.reload();
};

// Инициализация начальной позиции
window.onload = function () {
    currentX = targetX = window.innerWidth * SETTINGS.startX - SETTINGS.elementCursor.width() / 2
    currentY = targetY = window.innerHeight * SETTINGS.startY - SETTINGS.elementCursor.height() / 2

    // Устанавливаем начальную позицию
    SETTINGS.elementCursor.css({
        position: "fixed",
        left: currentX + "px",
        top: currentY + "px",
        pointerEvents: "none", // Чтобы не мешал взаимодействию с другими элементами
        zIndex: 9999,
    })

    RectZones = new Map();
    Object.entries(Zone).forEach(([key, value]) => {
        if (ZONES_SETTINGS[value]["element"] != null)
            RectZones.set(value, ZONES_SETTINGS[value]["element"][0].getBoundingClientRect());
    });

    // Запускаем анимацию
    if (!animationId) {
        animationId = requestAnimationFrame(updatePosition)
    }

    setTimeout(() => {
        startCursor()
    }, SETTINGS.timeout * 1000)

}

function handleMosemove(e) {
    // Обновляем целевую позицию (центрируем элемент на курсоре)
    targetX = e.clientX
    targetY = e.clientY

    // Запускаем анимацию, если она еще не запущена
    if (!animationId) {
        animationId = requestAnimationFrame(updatePosition)
    }
}

function handleBlur() {
    if (animationId) {
        cancelAnimationFrame(animationId)
        animationId = null
    }
}

function startCursor() {
    window.addEventListener("mousemove", handleMosemove)
    window.addEventListener("blur", handleBlur)
    IS_STOPPED = false
}

function stopCursor() {
    IS_STOPPED = true
    window.removeEventListener("mousemove", handleMosemove)
    window.removeEventListener("blur", handleBlur)
}

// 
// 
// 


// Функция для плавного обновления позиции
function updatePosition() {

    if (IS_STOPPED)
        return

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
    let foundZone = false;

    // Получаем все зоны и переворачиваем порядок для обратной проверки
    const zoneEntries = Object.entries(Zone);

    // Идем в обратном порядке (от последней зоны к первой)
    for (let i = zoneEntries.length - 1; i >= 0; i--) {

        const [key, value] = zoneEntries[i];
        if (isCursorInZone(value)) {
            foundZone = true;

            // Вход в новую зону
            if (CurrentZone !== value) {

                // Выход из предыдущей зоны
                handleOffCurrentZone()

                // Вход в новую зону
                CurrentZone = value;
                changeSrc(SETTINGS.elementCursor, ZONES_SETTINGS[CurrentZone]["imgCursor"]);
                handleOnCurrentZone()

            }

            break

        }
    }

    if (CurrentZone == Zone.POINT)
        console.log("POINT!")
    else
        console.log(CurrentZone)

    // Если курсор не в активной зоне, выходим из нее
    if (!foundZone) {
        handleOffCurrentZone()

        CurrentZone = Zone.NONE;
        changeSrc(SETTINGS.elementCursor, ZONES_SETTINGS[CurrentZone]["imgCursor"]);
        handleOnCurrentZone()
    }
}

function isCursorInZone(zoneType) {
    if (zoneType == Zone.NONE)
        return true

    // if (CurrentZone == Zone.POINT && zoneType == Zone.POINT)
    //     console.log("isCursorInZone(POINT)")

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
    if (ZONES_SETTINGS[CurrentZone]["handleOn"] == null)
        return
    ZONES_SETTINGS[CurrentZone]["handleOn"]();
}

function handleOffCurrentZone() {
    if (ZONES_SETTINGS[CurrentZone]["handleOff"] == null)
        return
    ZONES_SETTINGS[CurrentZone]["handleOff"]();
}

function changeSrc(elementImage, newSrc) {

    if (ZONES_SETTINGS[CurrentZone]["imgCursor"] == null
        || ZONES_SETTINGS[CurrentZone]["imgCursor"] == SETTINGS.elementCursor.attr('src'))
        return

    let speedAnimation = 10
    // Fade the image out
    elementImage.fadeOut(speedAnimation, function () {
        // Change the src attribute after the fade out is complete
        elementImage.attr("src", newSrc)

        // Wait for the new image to load before fading it in
        // Use .one('load', ...) to ensure the callback runs only once
        elementImage
            .one("load", function () {
                $(this).fadeIn(speedAnimation)
            })
            .each(function () {
                // Handle cached images that might not trigger the load event
                if (this.complete) $(this).trigger("load")
            })
    })
}
