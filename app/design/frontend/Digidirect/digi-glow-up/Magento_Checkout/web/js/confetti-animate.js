require(['jquery'], function ($) {
        const pageWrapper = document.querySelector('.page-wrapper');
        if (pageWrapper) {
            const partyCanvas = document.createElement('canvas');
            partyCanvas.id = 'party-canvas';
            partyCanvas.style.position = 'fixed';
            partyCanvas.style.top = '0';
            partyCanvas.style.left = '0';
            partyCanvas.style.width = '100%';
            partyCanvas.style.height = '100%';
            partyCanvas.style.pointerEvents = 'none';
            partyCanvas.style.zIndex = '9999';
            pageWrapper.parentNode.insertBefore(partyCanvas, pageWrapper.nextSibling);

            let animationFrameId;
            const canvas = document.getElementById("party-canvas");
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
                const gradient = gradientPairs[Math.floor(Math.random() * gradientPairs.length)];
                const width = Math.random() * 25 + 3;
                const height = Math.random() * 30 + 10;
                const originX = canvas.width / 2;
                const originY = -100;
                const angle = Math.random() * Math.PI * 2;
                const speed = Math.random() * 10 + 7;
                particles.push({
                    x: originX,
                    y: originY,
                    width: width,
                    height: height,
                    speedX: Math.cos(angle) * speed,
                    speedY: Math.sin(angle) * speed,
                    rotation: (Math.random() - 0.5) * 2,
                    gradient: gradient,
                    gravity: 0.25,
                    stopped: false,
                    life: 0
                });
            }

            function updateParticle(particle) {
                if (particle.stopped) return;
                particle.x += particle.speedX;
                particle.y += particle.speedY;
                particle.rotation += 0.03;
                particle.speedX *= 0.99;
                particle.speedY *= 0.975;
                particle.speedY += particle.gravity;
                if (
                    particle.y > canvas.height + 50 ||
                    particle.x < -50 ||
                    particle.x > canvas.width + 50
                ) {
                    particle.stopped = true;
                }
            }

            function drawParticle(particle) {
                ctx.save();
                ctx.translate(particle.x, particle.y);
                ctx.rotate(particle.rotation);
                const gradient = ctx.createLinearGradient(0, 0, particle.width, particle.height);
                gradient.addColorStop(0, particle.gradient[0]);
                gradient.addColorStop(1, particle.gradient[1]);
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, particle.width, particle.height);
                ctx.restore();
            }

            const totalDuration = 2500;
            const interval = 10;
            let particleCount = 0;
            const emitter = setInterval(() => {
                for (let i = 0; i < 5; i++) createParticle();
                particleCount++;
                if (particleCount * interval >= totalDuration) clearInterval(emitter);
            }, interval);

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach((p) => {
                    updateParticle(p);
                    drawParticle(p);
                });
                const allStopped = particles.every(p => p.stopped);
                const isEmitting = particleCount * interval < totalDuration;
                if (!allStopped || isEmitting) {
                    animationFrameId = requestAnimationFrame(animate);
                } else {
                    cancelAnimationFrame(animationFrameId);
                    canvas.style.display = 'none';
                }
            }
            animate();
        }
});
