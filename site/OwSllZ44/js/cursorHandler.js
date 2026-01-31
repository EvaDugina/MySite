const CURSOR_IMAGES = {
    NONE: "./images/cursors/none.png",
    DEFAULT: "./images/cursors/default.png",
    POINTER: "./images/cursors/pointer.png",
    POINTER_CLICKED: "./images/cursors/pointer-clicked.png",
    UNAVAILABLE: "./images/cursors/unavailable.png",
}

var $cursorElseElements = []

function createCursorElse($cursorsContainer, positionX, positionY) {
    let $element = $("<img>", {
        class: "cursor cursor-else not-allowed z-998",
        css: {
            position: "absolute",
            left: positionX + "%",
            top: positionY + "%",
        },
        src: "./images/cursors/pointer.png",
        alt: "муха",
    }).appendTo($cursorsContainer)

    $cursorElseElements.push($element)
}

// Иммитация клика
function clickCursorsElse() {
    $cursorElseElements.forEach(($this, index) => {
        setTimeout(
            async () => {
                changeCursorSrc(CURSOR_IMAGES.POINTER_CLICKED, $this)
                setTimeout(async () => {
                    changeCursorSrc(CURSOR_IMAGES.POINTER, $this)
                }, 50)
            },
            getRandomInt(0, 4) * 100,
        )
    })
}

async function ajaxSaveLastCursorPosition(uuid, percentX, percentY) {
    if (percentX == null || percentY == null) return

    const formData = new FormData()

    // Добавляем дополнительные данные, если нужно
    formData.append("flag-updateVisitorData", true)
    formData.append("uuid", uuid)
    formData.append("positionX", percentX)
    formData.append("positionY", percentY)

    let response = null
    try {
        response = await $.ajax({
            type: "POST",
            url: "./server/visitorController.php#content",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: "html",
        })
        response = JSON.parse(response.trim())
    } catch (error) {
        console.error("Ошибка запроса:", error)
        return null
    }

    return
}
