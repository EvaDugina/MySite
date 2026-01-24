async function flash(ids) {
    if (ids.length <= 0) return;

    const $flash = $("#div-flash-" + ids[0].toString());

    // Сбрасываем предыдущую анимацию
    $flash.removeClass("flash-animation");
    void $flash[0].offsetWidth; // Триггер перерисовки

    // Показываем и запускаем анимацию
    $flash.show();
    $flash.addClass("flash-animation");

    // Скрываем после завершения анимации
    await new Promise((resolve) => {
        setTimeout(() => {
            $flash.removeClass("flash-animation");
            $flash.hide();
            ids.shift();
            resolve();
        }, FLASH_SETTINGS.duration);
    });

    // Рекурсивно продолжаем для оставшихся ID
    if (ids.length > 0) {
        await flash(ids);
    }
}