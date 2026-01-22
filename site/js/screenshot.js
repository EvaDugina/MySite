// Функция для создания скриншота
async function captureScreenshot() {
    try {
        const canvas = await html2canvas(document.body, {
            scale: 1,
            useCORS: true, // Включаем CORS
            allowTaint: false, // НЕ разрешаем загрязнение canvas
            backgroundColor: "#ffffff",
            logging: true,
            onclone: function (clonedDoc) {
                // Обрабатываем изображения в клоне документа
                const images = clonedDoc.getElementsByTagName("img")
                for (let img of images) {
                    // Устанавливаем атрибут crossorigin для всех изображений
                    if (!img.crossOrigin) {
                        img.crossOrigin = "anonymous"
                    }
                }
            },
        })

        // Теперь можно использовать toDataURL
        const dataUrl = canvas.toDataURL("image/jpg")

        downloadFile(dataUrl)

        const blob = await new Promise((resolve) => {
            canvas.toBlob(resolve, "image/jpg", 1)
        })

        // Отправляем на сервер
        await uploadScreenshot(blob)
    } catch (error) {
        console.error("Ошибка создания скриншота:", error)
        // handleFallbackScreenshot();
    }
}

async function downloadFile(fileUrl) {
    let link = document.createElement("a")
    link.download = `${NAME_SCRENSHOT}.jpg`
    link.href = fileUrl
    link.click()
    link.remove()
}

// Функция отправки на сервер
async function uploadScreenshot(blob) {
    // Создаем FormData
    const formData = new FormData()

    formData.append("screenshot", blob, `${NAME_SCRENSHOT}_${Date.now()}.jpg`)

    // Добавляем дополнительные данные, если нужно
    // formData.append("flag-downloadScreenshot", true)
    formData.append("timestamp", new Date().toISOString())
    formData.append("pageUrl", window.location.href)

    let response = null
    try {
        response = await $.ajax({
            type: "POST",
            url: "/php/uploadScreenshots.php#content",
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
