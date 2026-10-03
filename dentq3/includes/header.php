<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ถ้ายังไม่ได้ล็อกอิน ให้เด้งกลับไปหน้า login
if(!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
// ตรวจสอบชื่อไฟล์ปัจจุบันเพื่อใช้ทำแถบไฮไลต์เมนู
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentQ - ระบบจัดการคลินิกทันตกรรม</title>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <canvas id="particles-canvas"></canvas>
    
    <!-- แถบเมนูด้านบน (Navbar) -->
    <nav class="navbar">
        <div class="logo" style="flex: 1;"><i data-lucide="activity"></i> DentQ</div>
        
        <!-- เปลี่ยนคลาสจาก menu เป็น stepper-menu -->
        <div class="stepper-menu" style="flex: 2;">
            <?php if($_SESSION['role'] == 'Patient'): ?>
                <a href="index.php" class="nav-step <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="user"></i></div>
                    <span class="step-text">หน้าแรก</span>
                </a>
                <a href="booking.php" class="nav-step <?php echo ($current_page == 'booking.php' || $current_page == 'process_booking.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="calendar-plus"></i></div>
                    <span class="step-text">นัดหมาย</span>
                </a>
                <a href="history.php" class="nav-step <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="file-text"></i></div>
                    <span class="step-text">ประวัติรักษา</span>
                </a>

            <?php elseif($_SESSION['role'] == 'Dentist'): ?>
                <!-- เพิ่มปุ่มแดชบอร์ด (หน้าแรก) สำหรับหมอ -->
                <a href="index.php" class="nav-step <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="layout-dashboard"></i></div>
                    <span class="step-text">แดชบอร์ด</span>
                </a>
                
                <a href="schedule.php" class="nav-step <?php echo ($current_page == 'schedule.php' || $current_page == 'treatment.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="calendar-days"></i></div>
                    <span class="step-text">ตารางนัดหมาย</span>
                </a>
                
                <a href="search_patient.php" class="nav-step <?php echo ($current_page == 'search_patient.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="search"></i></div>
                    <span class="step-text">ค้นหาผู้ป่วย</span>
                </a>

            <?php elseif($_SESSION['role'] == 'Admin'): ?>
                  <a href="index.php" class="nav-step <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="layout-dashboard"></i></div>
                    <span class="step-text">แดชบอร์ด</span>
                </a>
                <a href="patient_manage.php" class="nav-step <?php echo ($current_page == 'patient_manage.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="users"></i></div>
                    <span class="step-text">ข้อมูลคนไข้</span>
                </a>
                <a href="dentist_manage.php" class="nav-step <?php echo ($current_page == 'dentist_manage.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="stethoscope"></i></div>
                    <span class="step-text">ทันตแพทย์</span>
                </a>
                <a href="service_manage.php" class="nav-step <?php echo ($current_page == 'service_manage.php') ? 'active' : ''; ?>">
                    <div class="step-circle"><i data-lucide="list"></i></div>
                    <span class="step-text">จัดการบริการ</span>
                </a>
            <?php endif; ?>
        </div>

        <div class="user-profile" style="flex: 1; justify-content: flex-end;">
            <span><i data-lucide="user-circle"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="btn-logout"><i data-lucide="log-out"></i></a>
        </div>
    </nav>
    
    <!-- พื้นที่แสดงเนื้อหาหลัก -->
    <div class="main-content">