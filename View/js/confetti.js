const NUM_CONFETTI = 100;
const MIN_DURATION = 2000;
const MAX_DURATION = 4000;
const CONFETTI_WIDTH = 10;
const CONFETTI_HEIGHT = 20;
const CONFETTI_OPACITY = 0.7;
const COLORS = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#00ffff', '#ff00ff'];
const WIGGLE_FREQUENCY = 10;
const WIGGLE_AMPLITUDE = 10;
const HORIZONTAL_RANGE = 100;

function createConfetti() {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.backgroundColor = COLORS[Math.floor(Math.random() * COLORS.length)];
    confetti.style.left = `${Math.random() * window.innerWidth}px`;
    confetti.style.top = `${-CONFETTI_HEIGHT}px`;
    confetti.style.width = `${CONFETTI_WIDTH}px`;
    confetti.style.height = `${CONFETTI_HEIGHT}px`;
    confetti.style.opacity = CONFETTI_OPACITY;
    document.body.appendChild(confetti);
    return confetti;
}

function animateConfetti(confetti) {
    const duration = Math.random() * (MAX_DURATION - MIN_DURATION) + MIN_DURATION;
    const rotation = Math.random() * 360;
    const startX = Math.random() * window.innerWidth;
    const endX = startX + (Math.random() * HORIZONTAL_RANGE * 2 - HORIZONTAL_RANGE);
    const endY = window.innerHeight;
    const startTime = performance.now();

    function animate(time) {
        const progress = (time - startTime) / duration;
        if (progress < 1) {
            const x = startX + (endX - startX) * progress + Math.sin(progress * WIGGLE_FREQUENCY) * WIGGLE_AMPLITUDE;
            const y = endY * progress;
            confetti.style.transform = `translate(${x}px, ${y}px) rotate(${rotation * progress}deg)`;
            requestAnimationFrame(animate);
        } else {
            document.body.removeChild(confetti);
            createAndAnimateConfetti();
        }
    }

    requestAnimationFrame(animate);
}

function createAndAnimateConfetti() {
    const confetti = createConfetti();
    animateConfetti(confetti);
}

export function doConfettiEffect()
{
    for (let i = 0; i < NUM_CONFETTI; i++) {
        createAndAnimateConfetti();
    }
}