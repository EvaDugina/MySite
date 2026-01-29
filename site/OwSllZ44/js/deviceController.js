function isSensoreDevice() {
    // Проверка поддержки touch-событий
    const hasTouch =
        "ontouchstart" in window ||
        navigator.maxTouchPoints > 0 ||
        navigator.msMaxTouchPoints > 0

    // Дополнительная проверка для некоторых браузеров
    if (hasTouch) {
        return true
    }

    // Проверка через медиа-запрос
    const query = "(pointer: coarse)"
    if (window.matchMedia && window.matchMedia(query).matches) {
        return true
    }

    // User Agent как последний вариант
    const userAgent = navigator.userAgent.toLowerCase()
    const isMobile =
        /mobile|android|iphone|ipad|ipod|windows phone|blackberry|webos|opera mini|iemobile/i.test(
            userAgent,
        )

    return isMobile
}
