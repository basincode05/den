<?php
session_start();
// ตรวจสอบสิทธิ์ Dentist
if($_SESSION['role'] != 'Dentist'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

$dentist_id = $_SESSION['user_id'];

// ดึงข้อมูลคิวการนัดหมายของทันตแพทย์ท่านนี้ (เรียงจากวันที่และเวลาที่ใกล้ที่สุด)
$sql = "SELECT a.Appointment_ID, a.Appt_Date, a.Time_Slot, a.Status, 
               p.Name_Surname AS Patient_Name, s.Service_Name 
        FROM Appointment a
        JOIN Patient p ON a.Patient_ID = p.Patient_ID
        JOIN Service s ON a.Service_ID = s.Service_ID
        WHERE a.Dentist_ID = $dentist_id 
        ORDER BY a.Appt_Date ASC, a.Time_Slot ASC";
        
$result = $conn->query($sql);

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>ตารางนัดหมายของฉัน (My Schedule)</h2>
    <p>ตรวจสอบคิวผู้ป่วยและบันทึกผลการรักษา</p>
</div>

<div class="booking-container" style="max-width: 1000px; margin: 0 auto; background: #152238; padding: 30px; border-radius: 10px; border: 1px solid #2a4365;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
        <thead>
            <tr style="border-bottom: 2px solid #2a4365; color: #a9bcd0;">
                <th style="padding: 12px;">วันที่นัดหมาย</th>
                <th style="padding: 12px;">เวลา</th>
                <th style="padding: 12px;">ชื่อผู้ป่วย</th>
                <th style="padding: 12px;">บริการที่จอง</th>
                <th style="padding: 12px;">สถานะ</th>
                <th style="padding: 12px; text-align: center;">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #2a4365;">
                        <td style="padding: 12px;"><?php echo date('d/m/Y', strtotime($row['Appt_Date'])); ?></td>
                        <td style="padding: 12px;"><?php echo date('H:i', strtotime($row['Time_Slot'])); ?> น.</td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($row['Patient_Name']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($row['Service_Name']); ?></td>
                        <td style="padding: 12px;">
                            <?php 
                                $status = $row['Status'];
                                $badge_color = '#a9bcd0';
                                if($status == 'Pending') $badge_color = '#f39c12';
                                if($status == 'Confirmed') $badge_color = '#3498db';
                                if($status == 'Done') $badge_color = '#4CAF50';
                                if($status == 'Cancelled') $badge_color = '#ff4d4d';
                            ?>
                            <span style="background: <?php echo $badge_color; ?>; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                <?php echo $status; ?>
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if($status != 'Cancelled' && $status != 'Done'): ?>
                                <!-- ส่ง Appointment_ID ไปยังหน้าบันทึกการรักษา -->
                                <a href="treatment.php?appt_id=<?php echo $row['Appointment_ID']; ?>" 
                                   class="btn" style="padding: 8px 15px; font-size: 0.9rem; text-decoration: none;">บันทึกการรักษา</a>
                            <?php else: ?>
                                <span style="color: #a9bcd0; font-size: 0.9rem;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center; color: #a9bcd0;">ยังไม่มีคิวนัดหมาย</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>