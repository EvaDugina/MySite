//
// 1. НА ВСЕ ИЗОБРАЖЕНИЯ КОТОРЫЕ ДОЛЖНЫ ПОПАСТ В СКРИНШОТ ПОСТАВИТЬ: crossorigin="anonymous"
//

// Функция для создания скриншота
async function captureScreenshot() {
    await captureCanvas()

    try {
        const canvas = await html2canvas(document.body, {
            scale: 1,
            useCORS: true, // Включаем CORS
            allowTaint: false, // НЕ разрешаем загрязнение canvas
            backgroundColor: "#ffffff",
            logging: true,
            onclone: function (clonedDoc) {
                // Удаляем все изображения без crossOrigin="anonymous"
                const images = clonedDoc.querySelectorAll(
                    "img:not([crossOrigin='anonymous'])",
                )
                for (let img of images) {
                    img.parentNode.removeChild(img)
                }
            },
        })

        // Теперь можно использовать toDataURL
        let ext = "jpg"

        const dataUrl = canvas.toDataURL(`image/${ext}`)

        downloadFile(dataUrl, ext)

        const blob = await new Promise((resolve) => {
            canvas.toBlob(resolve, `image/${ext}`, 1)
        })

        // Отправляем на сервер
        await uploadScreenshot(
            `${SCREENSHOT_SETTINGS.screenshot_name}_${Date.now()}.${ext}`,
            blob,
        )
    } catch (error) {
        console.error("Ошибка создания скриншота:", error)
        // handleFallbackScreenshot();
    }
}

async function captureCanvas() {
    try {
        // Создаем скриншот только canvas элемента
        const canvas = await html2canvas(SCREENSHOT_SETTINGS.$canvas, {
            scale: 2, // Увеличиваем качество
            useCORS: true,
            allowTaint: false,
            backgroundColor: null, // Ключевой параметр - прозрачный фон
            logging: false,
            foreignObjectRendering: false, // Для лучшей поддержки прозрачности
            removeContainer: true,
        })

        // Создаем blob для отправки на сервер
        const blob = await new Promise((resolve) => {
            canvas.toBlob(resolve, "image/png", 1)
        })

        // Отправляем на сервер
        await uploadScreenshot(
            `${SCREENSHOT_SETTINGS.screenshot_name}_${Date.now()}.png`,
            blob,
        )
    } catch (error) {
        console.error("Ошибка создания скриншота canvas:", error)
        throw error
    }
}

async function downloadFile(fileUrl, ext) {
    let link = document.createElement("a")
    link.download = `${SCREENSHOT_SETTINGS.screenshot_name}.${ext}`
    link.href = fileUrl
    link.click()
    link.remove()
}

// Функция отправки на сервер
async function uploadScreenshot(fileName, blob) {
    // Создаем FormData
    const formData = new FormData()

    formData.append("screenshot", blob, fileName)

    // Добавляем дополнительные данные, если нужно
    // formData.append("flag-downloadScreenshot", true)
    formData.append("timestamp", new Date().toISOString())
    formData.append("pageUrl", window.location.href)

    let response = null
    try {
        response = await $.ajax({
            type: "POST",
            url: "/server/uploadScreenshots.php#content",
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

    return true
}
