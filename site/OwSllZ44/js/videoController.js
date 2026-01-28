const VIDEO = VIDEO_SETTINGS.$element[0]

async function showVideo() {
    // 1. Подготавливаем видео (загружаем, но не показываем)
    VIDEO.load()

    // 2. Ждем, когда видео сможет воспроизводиться
    await new Promise((resolve) => {
        if (VIDEO.readyState >= 2) {
            resolve()
        } else {
            VIDEO_SETTINGS.$element.on("canplay", resolve)
        }
    })

    // 3. Плавно показываем видео
    VIDEO_SETTINGS.$element.css("opacity", 0).removeClass("d-none")

    // 5. После завершения анимации скрываем изображение полностью
    setTimeout(() => {
        VIDEO_SETTINGS.$element.css("opacity", 1)
    }, 800) // Должно совпадать с длительностью transition (0.8s)
}

// Функция для безопасного запуска
function playVideo() {
    VIDEO.currentTime = 0
    VIDEO.play()
        .then(() => {
            // console.log("Видео воспроизводится");
        })
        .catch((error) => {
            console.error("Ошибка воспроизведения:", error.name, error.message)
        })
}

// Функция для остановки
function stopVideo() {
    VIDEO.pause()
    VIDEO.currentTime = 0
    // console.log("⏹️ Видео остановлено");
}
