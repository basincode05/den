</div> <!-- ปิด .main-content -->
    <script>
        const canvas = document.getElementById('particles-canvas');
        if(canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            let particlesArray = [];
            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 0.5;
                    this.speedX = Math.random() * 1 - 0.5;
                    this.speedY = Math.random() * 1 - 0.5;
                }
                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;
                    if(this.size > 0.2) this.size -= 0.01;
                    if(this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                    if(this.y < 0 || this.y > canvas.height) this.speedY *= -1;
                }
                draw() {
                    ctx.fillStyle = 'rgba(0, 243, 255, 0.8)';
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
            function init() {
                particlesArray = [];
                for (let i = 0; i < 100; i++) particlesArray.push(new Particle());
            }
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (let i = 0; i < particlesArray.length; i++) {
                    particlesArray[i].update();
                    particlesArray[i].draw();
                    for (let j = i; j < particlesArray.length; j++) {
                        const dx = particlesArray[i].x - particlesArray[j].x;
                        const dy = particlesArray[i].y - particlesArray[j].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < 120) {
                            ctx.beginPath();
                            ctx.strokeStyle = `rgba(0, 243, 255, ${1 - distance/120})`;
                            ctx.lineWidth = 0.5;
                            ctx.moveTo(particlesArray[i].x, particlesArray[i].y);
                            ctx.lineTo(particlesArray[j].x, particlesArray[j].y);
                            ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(animate);
            }
            window.addEventListener('resize', () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; init(); });
            init();
            animate();
        }
        lucide.createIcons();
    </script>
</body>
</html>