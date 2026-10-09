/**
 * mascot.js - Mascota interactiva (Robot Matemático "MultiBot")
 * Reacciona al cursor, al enfocar la contraseña, y a aciertos/errores
 */

document.addEventListener('DOMContentLoaded', () => {
    const mascotSvg = document.getElementById('mascot-svg');
    const leftPupil = document.getElementById('pupil-left');
    const rightPupil = document.getElementById('pupil-right');
    const eyeCoverLeft = document.getElementById('eye-cover-left');
    const eyeCoverRight = document.getElementById('eye-cover-right');
    const mascotMouth = document.getElementById('mascot-mouth');
    const mascotAntenna = document.getElementById('mascot-antenna-light');

    const passInput = document.getElementById('password');
    const userInput = document.getElementById('username');

    if (!mascotSvg) return;

    // Seguimiento del cursor con los ojos
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let isCoveringEyes = false;

    window.addEventListener('mousemove', (e) => {
        if (isCoveringEyes) return;
        mouseX = e.clientX;
        mouseY = e.clientY;

        const rect = mascotSvg.getBoundingClientRect();
        const mascotCenterX = rect.left + rect.width / 2;
        const mascotCenterY = rect.top + rect.height / 2;

        const deltaX = (mouseX - mascotCenterX) / (window.innerWidth / 2);
        const deltaY = (mouseY - mascotCenterY) / (window.innerHeight / 2);

        // Limitar movimiento pupila en rango de -6 a +6 px
        const moveX = Math.max(-6, Math.min(6, deltaX * 8));
        const moveY = Math.max(-5, Math.min(5, deltaY * 6));

        if (leftPupil && rightPupil) {
            leftPupil.setAttribute('transform', `translate(${moveX}, ${moveY})`);
            rightPupil.setAttribute('transform', `translate(${moveX}, ${moveY})`);
        }
    });

    // Tapar ojos cuando se enfoca la contraseña
    if (passInput) {
        passInput.addEventListener('focus', () => {
            isCoveringEyes = true;
            if (eyeCoverLeft && eyeCoverRight) {
                eyeCoverLeft.classList.add('covering');
                eyeCoverRight.classList.add('covering');
            }
            if (mascotMouth) {
                mascotMouth.setAttribute('d', 'M 42 74 Q 50 78 58 74'); // Boca curiosa
            }
        });

        passInput.addEventListener('blur', () => {
            isCoveringEyes = false;
            if (eyeCoverLeft && eyeCoverRight) {
                eyeCoverLeft.classList.remove('covering');
                eyeCoverRight.classList.remove('covering');
            }
            if (mascotMouth) {
                mascotMouth.setAttribute('d', 'M 40 73 Q 50 82 60 73'); // Sonrisa normal
            }
        });
    }

    // Funciones exportadas para estados
    window.mascotSetState = function (state) {
        if (!mascotSvg) return;

        if (state === 'error') {
            mascotSvg.classList.add('mascot-shake');
            if (mascotMouth) mascotMouth.setAttribute('d', 'M 40 78 Q 50 68 60 78'); // Boca triste/preocupada
            if (mascotAntenna) mascotAntenna.style.fill = '#ef4444'; // Antena roja
            setTimeout(() => {
                mascotSvg.classList.remove('mascot-shake');
                if (mascotMouth) mascotMouth.setAttribute('d', 'M 40 73 Q 50 82 60 73');
                if (mascotAntenna) mascotAntenna.style.fill = '#10b981';
            }, 1200);
        } else if (state === 'success') {
            mascotSvg.classList.add('mascot-celebrate');
            if (mascotMouth) mascotMouth.setAttribute('d', 'M 38 70 Q 50 86 62 70'); // Gran sonrisa
            if (mascotAntenna) mascotAntenna.style.fill = '#f59e0b'; // Antena dorada
        }
    };
});
