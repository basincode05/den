<?php
session_start();
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

    // 1. ตรวจสอบสิทธิ์ Dentist
    $sql_dentist = "SELECT * FROM Dentist WHERE Email = '$username' AND Password = '$password'";
    $res_dentist = $conn->query($sql_dentist);

    // 2. ตรวจสอบสิทธิ์ Admin
    $sql_admin = "SELECT * FROM Admin WHERE Username = '$username' AND Password = '$password'";
    $res_admin = $conn->query($sql_admin);

    if ($res_dentist->num_rows > 0) {
        $user = $res_dentist->fetch_assoc();
        $_SESSION['role'] = 'Dentist';
        $_SESSION['user_id'] = $user['Dentist_ID'];
        $_SESSION['user_name'] = $user['Name'];
        header("Location: dentist/index.php");
        exit();
    } elseif ($res_admin->num_rows > 0) {
        $user = $res_admin->fetch_assoc();
        $_SESSION['role'] = 'Admin';
        $_SESSION['user_id'] = $user['Admin_ID'];
        $_SESSION['user_name'] = $user['Name'];
        header("Location: admin/index.php");
        exit();
    } else {
        $error = "อีเมล/ชื่อผู้ใช้ หรือรหัสผ่านเจ้าหน้าที่ไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentQ - ระบบเจ้าหน้าที่</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <canvas id="particles-canvas"></canvas>
    <div class="bg-pattern"></div>
    
    <!-- ปุ่มกลับหน้าหลัก -->
    <div style="position: absolute; top: 30px; left: 40px; z-index: 100;">
        <a href="index.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; font-weight: 600; color: var(--text-muted); background: rgba(255, 255, 255, 0.9); border: 1px solid #e2e8f0; border-radius: 20px; text-decoration: none; transition: all 0.3s; font-size: 0.95rem;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> กลับหน้าหลักผู้ป่วย
        </a>
    </div>

    <!-- ฟอร์ม Login เจ้าหน้าที่ -->
    <div class="login-container" style="animation: floatUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <h2 style="background: linear-gradient(135deg, var(--secondary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">ระบบเจ้าหน้าที่</h2>
        <p>สำหรับทันตแพทย์และผู้ดูแลระบบ (Admin) เท่านั้น</p>
        
        <form method="POST" action="">
            <div class="input-group">
                <label>อีเมล หรือ Username</label>
                <input type="text" name="username" required placeholder="doctor@clinic.com / admin">
            </div>
            <div class="input-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <?php if($error != "") echo "<div class='error'>$error</div>"; ?>
            
            <button type="submit" class="btn" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%) !important;">เข้าสู่ระบบจัดการ</button>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>