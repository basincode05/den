<?php
session_start();
if($_SESSION['role'] != 'Admin'){
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';
$admin_id = $_SESSION['user_id'];

// ดึงสถิติภาพรวม
$total_patients = $conn->query("SELECT COUNT(*) FROM Patient")->fetch_row()[0];
$total_dentists = $conn->query("SELECT COUNT(*) FROM Dentist")->fetch_row()[0];
$total_appts_today = $conn->query("SELECT COUNT(*) FROM Appointment WHERE Appt_Date = CURDATE()")->fetch_row()[0];

// ดึงบริการที่มีคนจองเยอะที่สุด (ตัวอย่าง)
$top_services = $conn->query("SELECT s.Service_Name, COUNT(a.Appointment_ID) as hits 
                            FROM Appointment a 
                            JOIN Service s ON a.Service_ID = s.Service_ID 
                            GROUP BY a.Service_ID ORDER BY hits DESC LIMIT 3");

include '../includes/header.php'; 
?>

<!-- Premium Dashboard Header -->
<div style="background: linear-gradient(135deg, #4f46e5, #9333ea); border-radius: 28px; padding: 40px 50px; color: white; margin-bottom: 40px; box-shadow: 0 20px 40px rgba(147, 51, 234, 0.3); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between;">
    <!-- Abstract Shapes for Background -->
    <div style="position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 1;">
        <div style="display: inline-block; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.3);">
            <i data-lucide="settings" style="width: 16px; height: 16px; margin-right: 5px;"></i> System Administrator
        </div>
        <h2 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">ยินดีต้อนรับแอดมิน <?php echo htmlspecialchars($_SESSION['user_name']); ?> 🛡️</h2>
        <p style="font-size: 1.15rem; opacity: 0.9; max-width: 600px; line-height: 1.6;">ภาพรวมการบริหารงานคลินิก จัดการผู้ป่วย ทันตแพทย์ และตรวจสอบสถิติการใช้งานระบบ</p>
    </div>

    <!-- Background Big Icon -->
    <i data-lucide="layout-dashboard" style="width: 180px; height: 180px; color: rgba(255,255,255,0.15); position: absolute; right: 40px; transform: rotate(-5deg);"></i>
</div>

<div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- ฝั่งซ้าย: ข้อมูลส่วนตัว และ สถิติภาพรวม -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- ข้อมูลโปรไฟล์แอดมิน -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                    <i data-lucide="shield" style="width: 40px; height: 40px; color: white;"></i>
                </div>
                <h3 style="font-size: 1.25rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                <p style="color: var(--primary); font-weight: 600; font-size: 0.9rem;">Administrator</p>
            </div>
        </div>

        <!-- เมนูจัดการด่วน -->
        <div style="background: white; border: 1px solid var(--card-border); border-radius: 20px; padding: 25px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 10px;">
            <h4 style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">เมนูจัดการข้อมูล (Manage)</h4>
            <a href="patient_manage.php" class="btn" style="background: linear-gradient(135deg, var(--primary), var(--secondary)) !important; text-align: left; padding: 12px 20px; font-size: 1rem;"><i data-lucide="users" style="margin-right: 8px;"></i> จัดการผู้ป่วย</a>
            <a href="dentist_manage.php" class="btn" style="background: linear-gradient(135deg, var(--secondary), var(--accent)) !important; text-align: left; padding: 12px 20px; font-size: 1rem;"><i data-lucide="stethoscope" style="margin-right: 8px;"></i> จัดการทันตแพทย์</a>
            <a href="service_manage.php" class="btn" style="background: linear-gradient(135deg, #10b981, #059669) !important; text-align: left; padding: 12px 20px; font-size: 1rem;"><i data-lucide="clipboard-list" style="margin-right: 8px;"></i> จัดการบริการคลินิก</a>
        </div>
        
    </div>

    <!-- ฝั่งขวา: สถิติ (Stats) -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- 3 กล่องสถิติ (Overview) -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="booking-container" style="padding: 25px !important; text-align: center;">
                <div style="background: rgba(59, 130, 246, 0.1); color: var(--primary); width: 50px; height: 50px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i data-lucide="users"></i>
                </div>
                <h2 style="font-size: 2rem; color: var(--text-main); margin-bottom: 5px;"><?php echo $total_patients; ?></h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">ผู้ป่วยทั้งหมด</p>
            </div>
            
            <div class="booking-container" style="padding: 25px !important; text-align: center;">
                <div style="background: rgba(139, 92, 246, 0.1); color: var(--secondary); width: 50px; height: 50px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i data-lucide="stethoscope"></i>
                </div>
                <h2 style="font-size: 2rem; color: var(--text-main); margin-bottom: 5px;"><?php echo $total_dentists; ?></h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">ทันตแพทย์ทั้งหมด</p>
            </div>

            <div class="booking-container" style="padding: 25px !important; text-align: center;">
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 50px; height: 50px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i data-lucide="calendar-check"></i>
                </div>
                <h2 style="font-size: 2rem; color: var(--text-main); margin-bottom: 5px;"><?php echo $total_appts_today; ?></h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">นัดหมายวันนี้</p>
            </div>
        </div>

        <!-- บริการยอดนิยม -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--text-main);"><i data-lucide="trending-up" style="margin-right: 8px; color: var(--accent);"></i> บริการที่มีคนทำมากที่สุด</h3>
            </div>

            <?php if($top_services->num_rows > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php $rank = 1; while($srv = $top_services->fetch_assoc()): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; background: white; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="font-size: 1.2rem; font-weight: 800; color: #cbd5e1;">#<?php echo $rank++; ?></div>
                                <h4 style="font-size: 1.05rem; color: var(--text-main);"><?php echo htmlspecialchars($srv['Service_Name']); ?></h4>
                            </div>
                            <div style="font-size: 0.95rem; font-weight: 600; color: var(--primary);">
                                <?php echo $srv['hits']; ?> ครั้ง
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center; padding: 20px;">ยังไม่มีข้อมูลการใช้บริการ</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>