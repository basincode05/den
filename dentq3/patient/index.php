<?php
session_start();
if($_SESSION['role'] != 'Patient'){
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';
$patient_id = $_SESSION['user_id'];

// 1. ดึงข้อมูลคนไข้
$patient_query = $conn->query("SELECT * FROM Patient WHERE Patient_ID = $patient_id");
$patient_data = $patient_query->fetch_assoc();

// 2. ค้นหานัดหมายที่กำลังจะมาถึง (Upcoming)
$upcoming_sql = "SELECT a.Appointment_ID, a.Appt_Date, a.Time_Slot, a.Status, 
               s.Service_Name, d.Name AS Dentist_Name 
        FROM Appointment a
        JOIN Service s ON a.Service_ID = s.Service_ID
        JOIN Dentist d ON a.Dentist_ID = d.Dentist_ID
        WHERE a.Patient_ID = $patient_id 
        AND a.Appt_Date >= CURDATE() 
        AND a.Status IN ('Pending', 'Confirmed')
        ORDER BY a.Appt_Date ASC, a.Time_Slot ASC LIMIT 1";
$upcoming_res = $conn->query($upcoming_sql);
$upcoming_appt = ($upcoming_res->num_rows > 0) ? $upcoming_res->fetch_assoc() : null;

// 3. ดึงประวัติการรักษาล่าสุด (Recent)
$recent_sql = "SELECT a.Appt_Date, s.Service_Name, d.Name AS Dentist_Name 
        FROM Appointment a
        JOIN Service s ON a.Service_ID = s.Service_ID
        JOIN Dentist d ON a.Dentist_ID = d.Dentist_ID
        WHERE a.Patient_ID = $patient_id 
        AND a.Status = 'Done'
        ORDER BY a.Appt_Date DESC LIMIT 3";
$recent_res = $conn->query($recent_sql);

include '../includes/header.php'; 
?>

<!-- Premium Dashboard Header -->
<div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 28px; padding: 40px 50px; color: white; margin-bottom: 40px; box-shadow: 0 20px 40px rgba(59, 130, 246, 0.25); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between;">
    <!-- Abstract Shapes for Background -->
    <div style="position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -30%; right: 15%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 1;">
        <div style="display: inline-block; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.3);">
            <i data-lucide="smile" style="width: 16px; height: 16px; margin-right: 5px;"></i> ศูนย์ข้อมูลผู้ป่วย
        </div>
        <h2 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">สวัสดี, คุณ <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
        <p style="font-size: 1.15rem; opacity: 0.9; max-width: 600px; line-height: 1.6;">จัดการการนัดหมาย ดูประวัติการรักษา และอัปเดตข้อมูลสุขภาพฟันของคุณได้อย่างง่ายดายในที่เดียว</p>
    </div>

    <!-- Background Big Icon -->
    <i data-lucide="heart-pulse" style="width: 180px; height: 180px; color: rgba(255,255,255,0.1); position: absolute; right: 20px; transform: rotate(-15deg);"></i>
</div>

<div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- ฝั่งซ้าย: ข้อมูลส่วนตัว และ การแจ้งเตือน -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- ข้อมูลโปรไฟล์ -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);">
                    <i data-lucide="user" style="width: 40px; height: 40px; color: white;"></i>
                </div>
                <h3 style="font-size: 1.25rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($patient_data['Name_Surname']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">รหัสผู้ป่วย: HN-<?php echo str_pad($patient_id, 4, '0', STR_PAD_LEFT); ?></p>
            </div>
            <hr style="border: 0; border-top: 1px solid var(--card-border); margin: 20px 0;">
            <div style="font-size: 0.95rem; color: var(--text-main);">
                <p style="margin-bottom: 10px;"><i data-lucide="phone" style="width: 16px; margin-right: 8px; color: var(--primary);"></i> <?php echo htmlspecialchars($patient_data['Phone']); ?></p>
                <p style="margin-bottom: 10px;"><i data-lucide="mail" style="width: 16px; margin-right: 8px; color: var(--primary);"></i> <?php echo htmlspecialchars($patient_data['Email']); ?></p>
            </div>
        </div>

        <!-- Smart Alert -->
        <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1)); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 20px; padding: 25px; display: flex; align-items: flex-start; gap: 15px;">
            <i data-lucide="bell-ring" style="width: 30px; height: 30px; color: var(--primary); flex-shrink: 0;"></i>
            <div>
                <h4 style="color: var(--primary); margin-bottom: 5px;">ข้อแนะนำจากคลินิก</h4>
                <p style="font-size: 0.9rem; color: var(--text-main); line-height: 1.5;">อย่าลืมตรวจสุขภาพฟันและขูดหินปูนทุกๆ 6 เดือน เพื่อรอยยิ้มที่สดใสและสุขภาพช่องปากที่ดีนะครับ!</p>
            </div>
        </div>
        
    </div>

    <!-- ฝั่งขวา: นัดหมาย และ ประวัติ -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- นัดหมายครั้งถัดไป -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--secondary);"><i data-lucide="calendar-clock" style="margin-right: 8px;"></i> นัดหมายครั้งถัดไป (Upcoming)</h3>
                <a href="booking.php" class="btn" style="padding: 8px 16px; font-size: 0.9rem; width: auto; margin: 0 !important; box-shadow: none !important;">+ จองคิวใหม่</a>
            </div>
            
            <?php if($upcoming_appt): ?>
                <div style="background: white; border: 1px solid var(--card-border); border-radius: 16px; padding: 20px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <div style="background: #f1f5f9; padding: 15px; border-radius: 12px; text-align: center; min-width: 80px;">
                            <span style="display: block; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;"><?php echo date('M', strtotime($upcoming_appt['Appt_Date'])); ?></span>
                            <span style="display: block; font-size: 1.8rem; font-weight: 800; color: var(--primary); line-height: 1; margin: 5px 0;"><?php echo date('d', strtotime($upcoming_appt['Appt_Date'])); ?></span>
                            <span style="display: block; font-size: 0.85rem; color: var(--text-main); font-weight: 600;"><?php echo date('H:i', strtotime($upcoming_appt['Time_Slot'])); ?></span>
                        </div>
                        <div>
                            <h4 style="font-size: 1.2rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($upcoming_appt['Service_Name']); ?></h4>
                            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 8px;"><i data-lucide="user" style="width: 14px; margin-right: 4px;"></i> <?php echo htmlspecialchars($upcoming_appt['Dentist_Name']); ?></p>
                            
                            <?php 
                                $status = $upcoming_appt['Status'];
                                $badge_style = "";
                                if($status == 'Pending') $badge_style = "color: #d97706; background: linear-gradient(135deg, #fef3c7, #fde68a);";
                                elseif($status == 'Confirmed') $badge_style = "color: #2563eb; background: linear-gradient(135deg, #dbeafe, #bfdbfe);";
                            ?>
                            <span style="<?php echo $badge_style; ?> padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 700;"><?php echo $status; ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--card-border);">
                    <i data-lucide="calendar-x" style="width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="color: var(--text-muted);">คุณยังไม่มีคิวนัดหมายที่กำลังจะมาถึง</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ประวัติการรักษาล่าสุด -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--text-main);"><i data-lucide="activity" style="margin-right: 8px; color: #10b981;"></i> ประวัติการรักษาล่าสุด</h3>
                <a href="history.php" style="color: var(--primary); font-size: 0.9rem; font-weight: 600; text-decoration: none;">ดูทั้งหมด ➔</a>
            </div>

            <?php if($recent_res->num_rows > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php while($history = $recent_res->fetch_assoc()): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background: white; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="background: #dcfce7; color: #059669; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="check-circle" style="width: 20px;"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 1rem; color: var(--text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($history['Service_Name']); ?></h4>
                                    <p style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($history['Dentist_Name']); ?></p>
                                </div>
                            </div>
                            <div style="font-size: 0.9rem; font-weight: 500; color: var(--text-main);">
                                <?php echo date('d/m/Y', strtotime($history['Appt_Date'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center; padding: 20px;">ยังไม่มีประวัติการรักษา</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>