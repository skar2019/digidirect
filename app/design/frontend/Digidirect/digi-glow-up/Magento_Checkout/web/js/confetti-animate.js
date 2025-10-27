require(['jquery'], function ($) {
    function initConfetti() {
        const pageWrapper = document.querySelector('.page-wrapper');
        if (!pageWrapper) return;

        const canvas = document.createElement('canvas');
        canvas.id = 'party-canvas';
        Object.assign(canvas.style, {
            position: 'absolute',
            inset: '0',
            width: '100%',
            height: '100%',
            pointerEvents: 'none',
            zIndex: '9999'
        });
        pageWrapper.appendChild(canvas);

        const ctx = canvas.getContext("2d");
        const particles = [];
        const gradientPairs = [
            ["#00A1FF", "#422FD4"],
            ["#FF003C", "#FF455E"],
            ["#C20094", "#9C00C2"],
            ["#D1D100", "#93D100"],
            ["#FFA600", "#FFD100"]
        ];

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener("resize", resizeCanvas);

        function createParticle() {
            const g = gradientPairs[Math.floor(Math.random() * gradientPairs.length)];
            const size = Math.random() * 20 + 10;

            particles.push({
                x: canvas.width / 2,    // start at top center
                y: -100,                   // top of the page
                w: size,
                h: size * 1.2,
                // horizontal velocity spreads outward
                vx: (Math.random() - 0.5) * 40,
                // vertical velocity mostly downward but slightly randomized
                vy: Math.random() * 8 + 4,
                rot: Math.random() * Math.PI,
                g: g
            });
        }

        const duration = 3500;
        const startTime = performance.now();

        function animate(now) {
            const elapsed = now - startTime;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (elapsed < duration) {
                for (let i = 0; i < 6; i++) createParticle();
            }

            particles.forEach((p) => {
                p.x += p.vx;
                p.y += p.vy;
                p.vy += 0.25;
                p.vx *= 0.99;
                p.rot += 0.03;

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rot);
                const grd = ctx.createLinearGradient(0, 0, p.w, p.h);
                grd.addColorStop(0, p.g[0]);
                grd.addColorStop(1, p.g[1]);
                ctx.fillStyle = grd;
                ctx.fillRect(0, 0, p.w, p.h);
                ctx.restore();
            });

            if (elapsed < duration + 1500) {
                requestAnimationFrame(animate);
            } else {
                canvas.remove();
            }
        }

        requestAnimationFrame(animate);
    }

    // Run confetti once success message exists
    const observeInterval = setInterval(() => {
        if (document.querySelector('.checkout-success')) {
            clearInterval(observeInterval);
            initConfetti();
        }
    }, 100);
});
