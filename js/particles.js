/**
 * particles.js - Fondo dinámico de partículas y fórmulas matemáticas flotantes
 */

(function () {
    const canvas = document.getElementById('bg-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });

    const mathTokens = [
        '7 × 8 = 56', '9 × 9 = 81', '6 × 7 = 42', '5 × 5 = 25',
        '8 × 4 = 32', '3 × 9 = 27', '4 × 6 = 24', '2 × 8 = 16',
        '×', '÷', '+', '=', '★', '∞', 'π', '12', '49', '64', '100'
    ];

    const colors = [
        'rgba(99, 102, 241, 0.25)',  // Indigo
        'rgba(168, 85, 247, 0.25)',  // Purple
        'rgba(236, 72, 153, 0.22)',  // Pink
        'rgba(59, 130, 246, 0.25)',  // Blue
        'rgba(16, 185, 129, 0.22)',  // Emerald
        'rgba(245, 158, 11, 0.25)'   // Amber
    ];

    class FloatingItem {
        constructor() {
            this.reset(true);
        }

        reset(initial = false) {
            this.text = mathTokens[Math.floor(Math.random() * mathTokens.length)];
            this.color = colors[Math.floor(Math.random() * colors.length)];
            this.fontSize = Math.floor(Math.random() * 16) + 14; // 14px a 30px
            this.x = Math.random() * width;
            this.y = initial ? Math.random() * height : height + 30;
            this.speedY = Math.random() * 0.7 + 0.3; // velocidad hacia arriba
            this.speedX = (Math.random() - 0.5) * 0.4;
            this.opacity = Math.random() * 0.6 + 0.2;
            this.rotation = (Math.random() - 0.5) * 0.4;
            this.rotSpeed = (Math.random() - 0.5) * 0.01;
            this.pulse = Math.random() * Math.PI * 2;
        }

        update() {
            this.y -= this.speedY;
            this.x += this.speedX;
            this.rotation += this.rotSpeed;
            this.pulse += 0.03;

            if (this.y < -40 || this.x < -60 || this.x > width + 60) {
                this.reset(false);
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation);
            ctx.font = `bold ${this.fontSize}px 'Outfit', 'Segoe UI', sans-serif`;
            ctx.fillStyle = this.color;
            ctx.shadowBlur = 10;
            ctx.shadowColor = this.color;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(this.text, 0, 0);
            ctx.restore();
        }
    }

    const itemCount = Math.min(Math.floor((width * height) / 28000), 45);
    const items = [];
    for (let i = 0; i < itemCount; i++) {
        items.push(new FloatingItem());
    }

    // Dibujar orbes difuminados de fondo
    const orbs = [
        { x: width * 0.2, y: height * 0.25, r: 280, color: 'rgba(99, 102, 241, 0.15)', dx: 0.3, dy: 0.2 },
        { x: width * 0.8, y: height * 0.75, r: 320, color: 'rgba(236, 72, 153, 0.12)', dx: -0.2, dy: -0.3 },
        { x: width * 0.5, y: height * 0.5,  r: 220, color: 'rgba(16, 185, 129, 0.10)', dx: 0.2, dy: -0.2 }
    ];

    function drawOrbs() {
        orbs.forEach(orb => {
            orb.x += orb.dx;
            orb.y += orb.dy;
            if (orb.x < 0 || orb.x > width) orb.dx *= -1;
            if (orb.y < 0 || orb.y > height) orb.dy *= -1;

            const grad = ctx.createRadialGradient(orb.x, orb.y, 0, orb.x, orb.y, orb.r);
            grad.addColorStop(0, orb.color);
            grad.addColorStop(1, 'rgba(15, 23, 42, 0)');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.arc(orb.x, orb.y, orb.r, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);
        drawOrbs();

        items.forEach(item => {
            item.update();
            item.draw();
        });

        requestAnimationFrame(animate);
    }

    animate();
})();
