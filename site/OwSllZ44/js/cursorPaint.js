CANVAS.width = window.innerWidth - PADDING_CANVAS * 2
CANVAS.height = window.innerHeight - PADDING_CANVAS * 2

const CONTEXT = CANVAS.getContext("2d", { willReadFrequently: true })
CONTEXT.strokeStyle = "#000000"
CONTEXT.lineJoin = "round"
CONTEXT.lineWidth = 5

// BG_CANVAS.width = window.innerWidth - PADDING_CANVAS * 2;
// BG_CANVAS.height = window.innerHeight - PADDING_CANVAS * 2;

// const BG_CONTEXT = BG_CANVAS.getContext('2d');

PEN_COLOR.oninput = fill
// BACK_COLOR.oninput = setBackgroundColor;
// setBackgroundColor();

var clickX = []
var clickY = []
var clickDrag = []
var paint

/**
 * Add information where the user clicked at.
 * @param {number} x
 * @param {number} y
 * @return {boolean} dragging
 */
function addClick(x, y, dragging) {
    clickX.push(x)
    clickY.push(y)
    clickDrag.push(dragging)
}

/**
 * Redraw the complete canvas.
 */
function redraw() {
    // Clears the canvas
    CONTEXT.clearRect(0, 0, CONTEXT.canvas.width, CONTEXT.canvas.height)

    for (var i = 0; i < clickX.length; i += 1) {
        if (!clickDrag[i] && i == 0) {
            CONTEXT.beginPath()
            CONTEXT.moveTo(clickX[i], clickY[i])
            CONTEXT.stroke()
        } else if (!clickDrag[i] && i > 0) {
            CONTEXT.closePath()

            CONTEXT.beginPath()
            CONTEXT.moveTo(clickX[i], clickY[i])
            CONTEXT.stroke()
        } else {
            CONTEXT.lineTo(clickX[i], clickY[i])
            CONTEXT.stroke()
        }
    }
}

/**
 * Draw the newly added point.
 * @return {void}
 */
function drawNew() {
    var i = clickX.length - 1
    if (!clickDrag[i]) {
        if (clickX.length == 0) {
            CONTEXT.beginPath()
            CONTEXT.moveTo(clickX[i], clickY[i])
            CONTEXT.stroke()
        } else {
            CONTEXT.closePath()

            CONTEXT.beginPath()
            CONTEXT.moveTo(clickX[i], clickY[i])
            CONTEXT.stroke()
        }
    } else {
        CONTEXT.lineTo(clickX[i], clickY[i])
        CONTEXT.stroke()
    }
}

function mouseDownEventHandler(e) {
    paint = true
    var x = e.pageX - CANVAS.offsetLeft
    var y = e.pageY - CANVAS.offsetTop
    if (paint) {
        addClick(x, y, false)
        drawNew()
    }
}

function touchstartEventHandler(e) {
    paint = true
    if (paint) {
        addClick(
            e.touches[0].pageX - CANVAS.offsetLeft,
            e.touches[0].pageY - CANVAS.offsetTop,
            false,
        )
        drawNew()
    }
}

function mouseUpEventHandler(e) {
    CONTEXT.closePath()
    paint = false
}

function mouseMoveEventHandler(e) {
    var x = e.pageX - CANVAS.offsetLeft
    var y = e.pageY - CANVAS.offsetTop
    if (paint) {
        addClick(x, y, true)
        drawNew()
    }
}

function touchMoveEventHandler(e) {
    if (paint) {
        addClick(
            e.touches[0].pageX - CANVAS.offsetLeft,
            e.touches[0].pageY - CANVAS.offsetTop,
            true,
        )
        drawNew()
    }
}

function setUpHandler(isMouseandNotTouch, detectEvent) {
    removeRaceHandlers()
    if (isMouseandNotTouch) {
        CANVAS.addEventListener("mouseup", mouseUpEventHandler)
        CANVAS.addEventListener("mousemove", mouseMoveEventHandler)
        CANVAS.addEventListener("mousedown", mouseDownEventHandler)
        mouseDownEventHandler(detectEvent)
    } else {
        CANVAS.addEventListener("touchstart", touchstartEventHandler)
        CANVAS.addEventListener("touchmove", touchMoveEventHandler)
        CANVAS.addEventListener("touchend", mouseUpEventHandler)
        touchstartEventHandler(detectEvent)
    }
}

function mouseWins(e) {
    setUpHandler(true, e)
}

function touchWins(e) {
    setUpHandler(false, e)
}

function removeRaceHandlers() {
    CANVAS.removeEventListener("mousedown", mouseWins)
    CANVAS.removeEventListener("touchstart", touchWins)
}

function fill() {
    const color = pen_color.value
    CONTEXT.fillStyle = color
    CONTEXT.fill()
}

function isCanvasBlank() {
    const context = CANVAS.getContext("2d")
    const imageData = context.getImageData(0, 0, CANVAS.width, CANVAS.height)
    const data = imageData.data

    // Check if any of the color channels (R, G, B, or A) is non-zero
    for (let i = 0; i < data.length; i += 4) {
        if (
            data[i] !== 0 ||
            data[i + 1] !== 0 ||
            data[i + 2] !== 0 ||
            data[i + 3] !== 0
        ) {
            return false // Found a non-empty pixel
        }
    }

    return true // All pixels are transparent/blank
}

// function setBackgroundColor() {
//     const color = back_color.value;
//     BG_CONTEXT.fillStyle = color;
//     BG_CONTEXT.fillRect(0, 0, BG_CANVAS.width, BG_CANVAS.height);
// }

CANVAS.addEventListener("mousedown", mouseWins)
CANVAS.addEventListener("touchstart", touchWins)
