<?php
session_start();
// หากล็อกอินอยู่แล้วให้เด้งไปหน้า Dashboard
if(isset($_SESSION['role'])){
    header("Location: index.php");
    exit();
}
require_once 'includes/db.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string($_POST['password']);

    // ตรวจสอบอีเมลซ้ำ
    $check_email = $conn->query("SELECT Email FROM Patient WHERE Email = '$email'");
    if($check_email->num_rows > 0) {
        $error = "อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น";
    } else {
        $sql = "INSERT INTO Patient (Name_Surname, Phone, Email, Password) 
                VALUES ('$name', '$phone', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $success = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
        } else {
            $error = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentQ - สมัครสมาชิกใหม่</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <canvas id="particles-canvas"></canvas>
    <div class="bg-pattern"></div>
    <div class="login-container" style="display: flex; max-width: 950px; padding: 0; overflow: hidden; border-radius: 28px; border: 1px solid var(--card-border);">
        <!-- รูปภาพฝั่งซ้าย -->
        <div style="flex: 1.2; background: url('images/clinic_banner.jpg') center/cover; position: relative;">
            <div style="position: absolute; bottom: 30px; left: 30px; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                <h2 style="font-size: 2rem; color: white; margin-bottom: 5px;">DentQ Clinic</h2>
                <p style="opacity: 0.9;">ยินดีต้อนรับสู่คลินิกทันตกรรมมาตรฐานใหม่</p>
            </div>
        </div>
        
        <!-- ฟอร์มฝั่งขวา -->
        <div style="flex: 1; padding: 45px 50px; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <h2 style="text-align: left; font-size: 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">สมัครสมาชิก</h2>
            <p style="text-align: left; margin-bottom: 30px;">สร้างบัญชีผู้ป่วยใหม่เพื่อจองคิวออนไลน์</p>
            
            <?php if($error != "") echo "<div class='error'>$error</div>"; ?>
            <?php if($success != "") echo "<div style='color:var(--success); text-align:center; background: rgba(16, 185, 129, 0.1); padding: 15px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2);'>$success <br><br><a href='index.php'>คลิกที่นี่เพื่อเข้าสู่ระบบ</a></div>"; ?>
            
            <?php if($success == ""): ?>
            <form method="POST" action="register.php">
            <div class="input-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" name="name" required placeholder="เช่น สมหญิง รักฟันสวย">
            </div>
            <div class="input-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="text" name="phone" required placeholder="08X-XXX-XXXX">
            </div>
            <div class="input-group">
                <label>อีเมล</label>
                <input type="email" name="email" required placeholder="example@email.com">
            </div>
            <div class="input-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn" style="margin-top: 15px;">ยืนยันการสมัครสมาชิก</button>
        </form>
        <div style="text-align: center; margin-top: 25px;">
            <span style="color: var(--text-muted); font-size: 0.9rem;">มีบัญชีอยู่แล้ว? </span>
            <a href="index.php">เข้าสู่ระบบ</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        const canvas = document.getElementById('particles-canvas');
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
                    if (distance < 100) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(0, 243, 255, ${1 - distance/100})`;
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
    </script>
</body>
</html>