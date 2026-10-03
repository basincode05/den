<?php
session_start();
// ตรวจสอบว่าถ้า Login อยู่แล้ว ให้เด้งไปหน้า Dashboard
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'Patient') header("Location: patient/index.php");
    if($_SESSION['role'] == 'Dentist') header("Location: dentist/index.php");
    if($_SESSION['role'] == 'Admin') header("Location: admin/index.php");
    exit();
}

require_once 'includes/db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // ตรวจสอบสิทธิ์เฉพาะ Patient เท่านั้น
    $sql_patient = "SELECT * FROM Patient WHERE Email = '$username' AND Password = '$password'";
    $res_patient = $conn->query($sql_patient);

    if ($res_patient->num_rows > 0) {
        $user = $res_patient->fetch_assoc();
        $_SESSION['role'] = 'Patient';
        $_SESSION['user_id'] = $user['Patient_ID'];
        $_SESSION['user_name'] = $user['Name_Surname'];
        header("Location: patient/index.php");
        exit();
    } else {
        $error = "อีเมล หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentQ - คลินิกทันตกรรม</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <canvas id="particles-canvas"></canvas>
    <div class="bg-pattern"></div>
    <!-- ปุ่มมุมขวาบน สำหรับเจ้าหน้าที่ -->
    <div style="position: absolute; top: 30px; right: 40px; z-index: 100;">
        <a href="staff_login.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-weight: 600; color: var(--primary); background: rgba(255, 255, 255, 0.9); border: 2px solid var(--primary); border-radius: 20px; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.3s; font-size: 0.95rem;">
            <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i> สำหรับเจ้าหน้าที่
        </a>
    </div>
    
    <?php
        $show_login = ($error != "") ? "block" : "none";
        $show_landing = ($error != "") ? "none" : "block";
    ?>

    <!-- หน้าต่างโฆษณาคลินิก (Showcase - Two Column Layout) -->
    <div id="landingShowcase" style="display: <?php echo $show_landing; ?>; width: 100%; max-width: 1200px; padding: 40px; animation: floatUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 60px; flex-wrap: wrap;">
            
            <!-- Left Column: Text & Buttons -->
            <div style="flex: 1; min-width: 400px; text-align: left;">
                <div style="display: inline-block; background: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 20px; border: 1px solid rgba(59, 130, 246, 0.2);">
                    <i data-lucide="sparkles" style="width: 16px; height: 16px; margin-right: 5px;"></i> ระบบจัดการนัดหมายคลินิกทันตกรรม
                </div>
                <h1 style="font-size: 4.5rem; font-weight: 800; line-height: 1.1; color: var(--text-main); margin-bottom: 25px; letter-spacing: -2px;">
                    DentQ<br>
                    <span style="background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">ระบบจัดการคลินิก<br>ยุคใหม่</span>
                </h1>
                <p style="font-size: 1.25rem; color: var(--text-muted); margin-bottom: 40px; line-height: 1.7; max-width: 500px;">
                    รวมทุกงานของคลินิกไว้ในที่เดียว ตั้งแต่การจองนัดหมายออนไลน์ ตารางแพทย์ ไปจนถึงประวัติการรักษา ออกแบบมาเพื่อคลินิกทันตกรรมโดยเฉพาะ
                </p>
                <div style="display: flex; gap: 15px;">
                    <button onclick="document.getElementById('landingShowcase').style.display='none'; document.getElementById('loginForm').style.display='block';" class="btn" style="padding: 16px 32px; font-size: 1.1rem; width: auto;">เข้าสู่ระบบ</button>
                    <a href="register.php" style="display: inline-block; padding: 16px 32px; font-size: 1.1rem; font-weight: 600; color: var(--text-main); background: #ffffff; border: 2px solid #e2e8f0; border-radius: 16px; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">สมัครสมาชิกใหม่</a>
                </div>
            </div>

            <!-- Right Column: Interactive Circular Infographic -->
            <div style="flex: 1; position: relative; min-width: 450px; height: 500px; display: flex; align-items: center; justify-content: center;">
                
                <!-- Glowing Background Blob -->
                <div style="position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, rgba(139, 92, 246, 0.2) 40%, transparent 70%); filter: blur(40px); z-index: 0; animation: pulseGlow 4s infinite alternate;"></div>

                <!-- Center 3D Image (Restored & Levitation added) -->
                <div style="position: absolute; background: url('images/dental_3d.jpg') center/cover; width: 140px; height: 140px; border-radius: 50%; box-shadow: 0 0 40px rgba(0, 243, 255, 0.6), inset 0 0 20px rgba(255,255,255,0.8); z-index: 10; border: 4px solid rgba(255,255,255,0.9); animation: levitate 3s ease-in-out infinite;"></div>
                
                <!-- Center Circle Outline (Orbit Path) -->
                <div style="position: absolute; width: 360px; height: 360px; border: 2px dashed rgba(0, 243, 255, 0.4); border-radius: 50%; box-shadow: 0 0 20px rgba(0, 243, 255, 0.2), inset 0 0 20px rgba(0, 243, 255, 0.2); animation: spinSlow 40s linear infinite; z-index: 5;">
                    
                    <!-- Node 1 (Top) - จองนัดออนไลน์ -->
                    <div class="orbit-node" style="position: absolute; top: 0%; left: 50%; animation: spinReverse 40s linear infinite; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); padding: 20px; border-radius: 50%; width: 140px; height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2), inset 0 0 15px rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.6); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer;" onmouseover="this.style.transform='translate(-50%, -50%) scale(1.15) rotate(0deg)'; this.style.background='rgba(255,255,255,0.9)';" onmouseout="this.style.transform='translate(-50%, -50%) rotate(0deg)'; this.style.background='rgba(255,255,255,0.15)';">
                        <i data-lucide="calendar-plus" style="width: 35px; height: 35px; color: var(--primary); margin-bottom: 10px; filter: drop-shadow(0 2px 4px rgba(59,130,246,0.4));"></i>
                        <h4 style="font-size: 0.85rem; margin:0; font-weight: 700; color: var(--text-main);">จองนัดออนไลน์</h4>
                    </div>

                    <!-- Node 2 (Bottom Right) - ประวัติรักษา -->
                    <div class="orbit-node" style="position: absolute; top: 75%; left: 93.3%; animation: spinReverse 40s linear infinite; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); padding: 20px; border-radius: 50%; width: 140px; height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2), inset 0 0 15px rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.6); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer;" onmouseover="this.style.transform='translate(-50%, -50%) scale(1.15) rotate(0deg)'; this.style.background='rgba(255,255,255,0.9)';" onmouseout="this.style.transform='translate(-50%, -50%) rotate(0deg)'; this.style.background='rgba(255,255,255,0.15)';">
                        <i data-lucide="history" style="width: 35px; height: 35px; color: var(--accent); margin-bottom: 10px; filter: drop-shadow(0 2px 4px rgba(139,92,246,0.4));"></i>
                        <h4 style="font-size: 0.85rem; margin:0; font-weight: 700; color: var(--text-main);">ประวัติรักษา</h4>
                    </div>

                    <!-- Node 3 (Bottom Left) - ปลอดภัย -->
                    <div class="orbit-node" style="position: absolute; top: 75%; left: 6.7%; animation: spinReverse 40s linear infinite; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); padding: 20px; border-radius: 50%; width: 140px; height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2), inset 0 0 15px rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.6); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer;" onmouseover="this.style.transform='translate(-50%, -50%) scale(1.15) rotate(0deg)'; this.style.background='rgba(255,255,255,0.9)';" onmouseout="this.style.transform='translate(-50%, -50%) rotate(0deg)'; this.style.background='rgba(255,255,255,0.15)';">
                        <i data-lucide="shield-check" style="width: 35px; height: 35px; color: #10b981; margin-bottom: 10px; filter: drop-shadow(0 2px 4px rgba(16,185,129,0.4));"></i>
                        <h4 style="font-size: 0.85rem; margin:0; font-weight: 700; color: var(--text-main);">ปลอดภัย 100%</h4>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- สไตล์พิเศษสำหรับหน้า Landing -->
    <style>
        @keyframes levitate {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); box-shadow: 0 15px 50px rgba(0, 243, 255, 0.8), inset 0 0 25px rgba(255,255,255,0.9); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulseGlow {
            0% { opacity: 0.4; transform: scale(0.9); }
            100% { opacity: 0.8; transform: scale(1.1); }
        }
        @keyframes spinSlow {
            100% { transform: rotate(360deg); }
        }

        @keyframes spinReverse {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(-360deg); }
        }
        
        /* Hover effect สำหรับปุ่มรอง */
        /* Hover effect สำหรับปุ่มรอง */
        a[href="register.php"]:hover {
            border-color: var(--primary) !important;
            color: var(--primary) !important;
            transform: translateY(-2px);
        }
    </style>

    <!-- หน้าต่าง Login (ซ่อนไว้ก่อน) -->
    <div id="loginForm" class="login-container" style="display: <?php echo $show_login; ?>; animation: floatUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <h2>DentQ</h2>
        <p>ระบบจัดการนัดหมายคลินิกทันตกรรม</p>
        
        <form method="POST" action="">
            <div class="input-group">
                <label>อีเมล หรือ Username</label>
                <input type="text" name="username" required placeholder="example@email.com / admin">
            </div>
            <div class="input-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <?php if($error != "") echo "<div class='error'>$error</div>"; ?>
            
            <button type="submit" class="btn">เข้าสู่ระบบ (Login)</button>
            <div style="text-align: center; margin-top: 25px;">
                <span style="color: var(--text-muted); font-size: 0.95rem;">ยังไม่มีบัญชีผู้ใช้ใช่ไหม? </span>
                <a href="register.php">สมัครสมาชิกใหม่ที่นี่</a>
            </div>
        </form>
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
        lucide.createIcons();
    </script>
</body>
</html>