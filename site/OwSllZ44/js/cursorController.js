var targetX = 0;
var targetY = 0;
var currentX = 0;
var currentY = 0;
var velocityX = 0;
var velocityY = 0;

var animationId = null;

// Функция для плавного обновления позиции
function updatePosition() {
    // Рассчитываем силу (разница между текущей и целевой позицией)
    const forceX = (targetX - currentX) * settings.stiffness;
    const forceY = (targetY - currentY) * settings.stiffness;
    
    // Ускорение = сила / масса
    const accelerationX = forceX / settings.mass;
    const accelerationY = forceY / settings.mass;
    
    // Обновляем скорость с учетом ускорения и затухания
    velocityX = (velocityX + accelerationX) * settings.damping;
    velocityY = (velocityY + accelerationY) * settings.damping;
    
    // Ограничиваем максимальную скорость
    const speed = Math.sqrt(velocityX * velocityX + velocityY * velocityY);
    if (speed > settings.maxSpeed) {
        velocityX = (velocityX / speed) * settings.maxSpeed;
        velocityY = (velocityY / speed) * settings.maxSpeed;
    }
    
    // Обновляем позицию
    currentX += velocityX;
    currentY += velocityY;
    
    // Применяем к элементу
    ELEMENT_CURSOR.css({
        left: currentX + 'px',
        top: currentY + 'px'
    });

    updateCurrentZone();
    
    // Продолжаем анимацию
    animationId = requestAnimationFrame(updatePosition);
}

// Инициализация начальной позиции
document.addEventListener('DOMContentLoaded', () => {
    const ELEMENT_CURSOR = $("#img-cursor");
    currentX = targetX = window.innerWidth * 0.9 - (ELEMENT_CURSOR.width() / 2);
    currentY = targetY = window.innerHeight * 0.25 - (ELEMENT_CURSOR.height() / 2);
    
    // Устанавливаем начальную позицию
    ELEMENT_CURSOR.css({
        position: 'fixed',
        left: currentX + 'px',
        top: currentY + 'px',
        pointerEvents: 'none', // Чтобы не мешал взаимодействию с другими элементами
        zIndex: 9999
    });
    
    // Запускаем анимацию
    if (!animationId) {
        animationId = requestAnimationFrame(updatePosition);
    }
});

setTimeout(() => {

    // Обработчик движения мыши
    window.addEventListener('mousemove', (e) => {
        // Обновляем целевую позицию (центрируем элемент на курсоре)
        targetX = e.clientX - ($("#img-cursor").width() / 4) || 0;
        // targetY = e.clientY - ($("#img-cursor").height() / 2) || 0;
        // targetX = e.clientX;
        targetY = e.clientY;
        
        // Запускаем анимацию, если она еще не запущена
        if (!animationId) {
            animationId = requestAnimationFrame(updatePosition);
        }
    });

    // Останавливаем анимацию при потере фокуса
    window.addEventListener('blur', () => {
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
    });

}, TIMEOUT * 1000);

function isPosInElementBoundary(element) {
    let radius = 0;
    const rect = element[0].getBoundingClientRect();
    return (
        currentX + radius >= rect.left &&
        currentX - radius <= rect.right &&
        currentY + radius >= rect.top &&
        currentY - radius <= rect.bottom
    );
}

function changeSrc(elementImage, newSrc) {
    let speedAnimation = 10;
    // Fade the image out
    elementImage.fadeOut(speedAnimation, function () {
        // Change the src attribute after the fade out is complete
        elementImage.attr('src', newSrc);

        // Wait for the new image to load before fading it in
        // Use .one('load', ...) to ensure the callback runs only once
        elementImage.one('load', function() {
            $(this).fadeIn(speedAnimation);
        }).each(function() {
            // Handle cached images that might not trigger the load event
            if(this.complete) $(this).trigger('load');
        });
    });
}