<?php
session_start();
if($_SESSION['role'] != 'Dentist'){
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';
$dentist_id = $_SESSION['user_id'];

// ดึงข้อมูลหมอ
$dentist_query = $conn->query("SELECT * FROM Dentist WHERE Dentist_ID = $dentist_id");
$dentist_data = $dentist_query->fetch_assoc();

// ดึงคิวงานของวันนี้
$today_sql = "SELECT a.Appointment_ID, a.Time_Slot, a.Status, 
               p.Name_Surname AS Patient_Name, s.Service_Name 
        FROM Appointment a
        JOIN Patient p ON a.Patient_ID = p.Patient_ID
        JOIN Service s ON a.Service_ID = s.Service_ID
        WHERE a.Dentist_ID = $dentist_id 
        AND a.Appt_Date = CURDATE()
        ORDER BY a.Time_Slot ASC";
$today_res = $conn->query($today_sql);
$total_patients_today = $today_res->num_rows;

include '../includes/header.php'; 
?>

<!-- Premium Dashboard Header -->
<div style="background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 28px; padding: 40px 50px; color: white; margin-bottom: 40px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.4); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1);">
    <!-- Abstract Shapes for Background -->
    <div style="position: absolute; top: 0; right: 0; width: 100%; height: 100%; background: linear-gradient(90deg, transparent 50%, rgba(16, 185, 129, 0.1) 100%);"></div>
    <div style="position: absolute; top: -50px; right: 50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 1;">
        <div style="display: inline-block; background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 15px; border: 1px solid rgba(16, 185, 129, 0.3);">
            <i data-lucide="stethoscope" style="width: 16px; height: 16px; margin-right: 5px;"></i> Dentist Dashboard
        </div>
        <h2 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -1px;">สวัสดีครับคุณหมอ <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👨‍⚕️</h2>
        <p style="font-size: 1.15rem; color: #94a3b8; max-width: 600px; line-height: 1.6;">วันนี้มีคิวการรักษาทั้งหมด <strong style="color: white;"><?php echo $total_patients_today; ?> เคส</strong> พร้อมสร้างรอยยิ้มที่สวยงามให้คนไข้แล้วหรือยังครับ?</p>
    </div>

    <!-- Background Big Icon -->
    <i data-lucide="clipboard-list" style="width: 180px; height: 180px; color: rgba(255,255,255,0.05); position: absolute; right: 40px; transform: rotate(10deg);"></i>
</div>

<div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- ฝั่งซ้าย: ข้อมูลส่วนตัว และ สถิติ -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- ข้อมูลโปรไฟล์หมอ -->
        <div class="booking-container" style="padding: 30px !important;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--secondary), var(--accent)); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);">
                    <i data-lucide="stethoscope" style="width: 40px; height: 40px; color: white;"></i>
                </div>
                <h3 style="font-size: 1.25rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($dentist_data['Name']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">ความเชี่ยวชาญ: <span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($dentist_data['Specialty']); ?></span></p>
            </div>
            <hr style="border: 0; border-top: 1px solid var(--card-border); margin: 20px 0;">
            <div style="font-size: 0.95rem; color: var(--text-main);">
                <p style="margin-bottom: 10px;"><i data-lucide="phone" style="width: 16px; margin-right: 8px; color: var(--secondary);"></i> <?php echo htmlspecialchars($dentist_data['Phone']); ?></p>
            </div>
        </div>

        <!-- สถิติงานวันนี้ -->
        <div style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 20px; padding: 25px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="color: #059669; margin-bottom: 5px;">จำนวนเคสวันนี้</h4>
                <p style="font-size: 0.9rem; color: #15803d;">นัดหมายทั้งหมดในตาราง</p>
            </div>
            <div style="font-size: 2.5rem; font-weight: 800; color: #059669; line-height: 1;">
                <?php echo $total_patients_today; ?>
            </div>
        </div>

        <a href="search_patient.php" class="btn" style="text-align: center; padding: 15px;"><i data-lucide="search" style="margin-right: 8px;"></i> ค้นหาประวัติผู้ป่วย</a>
        
    </div>

    <!-- ฝั่งขวา: ตารางคิวงานวันนี้ -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <div class="booking-container" style="padding: 30px !important;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--primary);"><i data-lucide="calendar-days" style="margin-right: 8px;"></i> คิวงานวันนี้ (Today's Schedule)</h3>
                <a href="schedule.php" style="color: var(--secondary); font-size: 0.9rem; font-weight: 600; text-decoration: none;">จัดการคิวทั้งหมด ➔</a>
            </div>

            <?php if($today_res->num_rows > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php while($appt = $today_res->fetch_assoc()): ?>
                        <div style="background: white; border: 1px solid var(--card-border); border-radius: 16px; padding: 15px 20px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <div style="background: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 10px 15px; border-radius: 12px; font-weight: 800; font-size: 1.1rem;">
                                    <?php echo date('H:i', strtotime($appt['Time_Slot'])); ?>
                                </div>
                                <div>
                                    <h4 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($appt['Patient_Name']); ?></h4>
                                    <p style="color: var(--text-muted); font-size: 0.9rem;"><i data-lucide="activity" style="width: 14px; margin-right: 4px;"></i> <?php echo htmlspecialchars($appt['Service_Name']); ?></p>
                                </div>
                            </div>
                            <div>
                                <?php 
                                    $status = $appt['Status'];
                                    $badge_style = "";
                                    if($status == 'Pending') $badge_style = "color: #d97706; background: linear-gradient(135deg, #fef3c7, #fde68a);";
                                    elseif($status == 'Confirmed') $badge_style = "color: #2563eb; background: linear-gradient(135deg, #dbeafe, #bfdbfe);";
                                    elseif($status == 'Done') $badge_style = "color: #059669; background: linear-gradient(135deg, #dcfce7, #bbf7d0);";
                                    else $badge_style = "color: #dc2626; background: linear-gradient(135deg, #fee2e2, #fecaca);";
                                ?>
                                <span style="<?php echo $badge_style; ?> padding: 6px 14px; border-radius: 12px; font-size: 0.85rem; font-weight: 700;"><?php echo $status; ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: rgba(255,255,255,0.5); border-radius: 16px; border: 1px dashed var(--card-border);">
                    <i data-lucide="coffee" style="width: 45px; height: 45px; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <p style="color: var(--text-muted); font-size: 1.1rem;">วันนี้ไม่มีคิวงาน สามารถพักผ่อนได้เลยครับคุณหมอ</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>