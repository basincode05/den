<?php
session_start();
// ตรวจสอบสิทธิ์ Patient
if($_SESSION['role'] != 'Patient'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

$patient_id = $_SESSION['user_id'];

// ดึงข้อมูลประวัติการนัดหมายของผู้ป่วยคนนี้ พร้อมชื่อหมอและบริการ (ใช้ JOIN)
$sql = "SELECT a.Appointment_ID, a.Appt_Date, a.Time_Slot, a.Status, 
               s.Service_Name, d.Name AS Dentist_Name 
        FROM Appointment a
        JOIN Service s ON a.Service_ID = s.Service_ID
        JOIN Dentist d ON a.Dentist_ID = d.Dentist_ID
        WHERE a.Patient_ID = $patient_id
        ORDER BY a.Appt_Date DESC, a.Time_Slot DESC";
        
$result = $conn->query($sql);

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>ประวัติการรักษาและการนัดหมาย (History)</h2>
    <p>ตรวจสอบสถานะคิวและการรักษาย้อนหลังของคุณ</p>
</div>

<div class="booking-container" style="max-width: 900px; margin: 0 auto; background: #152238; padding: 30px; border-radius: 10px; border: 1px solid #2a4365;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
        <thead>
            <tr style="border-bottom: 2px solid #2a4365; color: #a9bcd0;">
                <th style="padding: 12px;">วันที่นัดหมาย</th>
                <th style="padding: 12px;">เวลา</th>
                <th style="padding: 12px;">บริการ</th>
                <th style="padding: 12px;">ทันตแพทย์</th>
                <th style="padding: 12px;">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #2a4365;">
                        <!-- แปลงรูปแบบวันที่ให้อ่านง่าย -->
                        <td style="padding: 12px;"><?php echo date('d/m/Y', strtotime($row['Appt_Date'])); ?></td>
                        <td style="padding: 12px;"><?php echo date('H:i', strtotime($row['Time_Slot'])); ?> น.</td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($row['Service_Name']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($row['Dentist_Name']); ?></td>
                        <td style="padding: 12px;">
                            <?php 
                                // กำหนดสีของสถานะให้แตกต่างกัน
                                $status = $row['Status'];
                                $badge_color = '#a9bcd0'; // Default
                                if($status == 'Pending') $badge_color = '#f39c12';
                                if($status == 'Confirmed') $badge_color = '#3498db';
                                if($status == 'Done') $badge_color = '#4CAF50';
                                if($status == 'Cancelled') $badge_color = '#ff4d4d';
                            ?>
                            <span style="background: <?php echo $badge_color; ?>; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                <?php echo $status; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 20px; text-align: center; color: #a9bcd0;">ยังไม่มีประวัติการทำรายการ</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>