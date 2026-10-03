<?php
session_start();
// ตรวจสอบสิทธิ์ Patient
if($_SESSION['role'] != 'Patient'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION['user_id'];
    $service_id = (int)$_POST['service_id'];
    $dentist_id = (int)$_POST['dentist_id'];
    $appt_date = $conn->real_escape_string($_POST['appt_date']);
    $time_slot = $conn->real_escape_string($_POST['time_slot']);

    include '../includes/header.php'; 

    echo '<div class="dashboard-header">';
    echo '<h2>ผลการทำรายการจองนัดหมาย</h2>';
    echo '</div>';

    echo '<div class="booking-container" style="max-width: 600px; margin: 0 auto; background: #152238; padding: 40px; border-radius: 10px; border: 1px solid #2a4365; text-align: center;">';

    // 1. ตรวจสอบคิวซ้อน (Double Booking Check)
    // เช็คว่าในวันที่และเวลานี้ ทันตแพทย์คนนี้มีคิวที่ยังไม่ถูกยกเลิกหรือไม่
    $check_sql = "SELECT * FROM Appointment 
                  WHERE Dentist_ID = $dentist_id 
                  AND Appt_Date = '$appt_date' 
                  AND Time_Slot = '$time_slot' 
                  AND Status != 'Cancelled'";
                  
    $check_res = $conn->query($check_sql);

    if ($check_res->num_rows > 0) {
        // กรณีคิวซ้อน: แจ้งเตือนผู้ใช้งาน (ตาม Flowchart)
        echo '<h3 style="color: #ff4d4d; font-size: 1.5rem;">❌ ช่วงเวลานี้ถูกจองแล้ว</h3>';
        echo '<p style="color: #a9bcd0; margin-top: 15px;">ขออภัย ทันตแพทย์ที่คุณเลือกมีคิวงานในช่วงเวลานี้แล้ว กรุณาเลือกช่วงเวลาอื่นครับ</p>';
        echo '<a href="booking.php" class="btn" style="display: inline-block; margin-top: 25px; background: #2a4365; width: auto; padding: 10px 30px;">กลับไปเลือกเวลาใหม่</a>';
    } else {
        // กรณีคิวว่าง: บันทึกข้อมูลลงตาราง Appointment (สถานะเริ่มต้นคือ Pending)
        $insert_sql = "INSERT INTO Appointment (Patient_ID, Dentist_ID, Service_ID, Appt_Date, Time_Slot, Status)
                       VALUES ($patient_id, $dentist_id, $service_id, '$appt_date', '$time_slot', 'Pending')";
        
        if ($conn->query($insert_sql) === TRUE) {
            echo '<h3 style="color: #4CAF50; font-size: 1.5rem;">✅ จองนัดหมายสำเร็จ</h3>';
            echo '<p style="color: #a9bcd0; margin-top: 15px;">ระบบได้บันทึกข้อมูลการจองของคุณเรียบร้อยแล้ว (สถานะ: รอดำเนินการ)</p>';
            echo '<a href="history.php" class="btn" style="display: inline-block; margin-top: 25px; width: auto; padding: 10px 30px;">ดูประวัติการจองของฉัน</a>';
        } else {
            echo '<h3 style="color: #ff4d4d; font-size: 1.5rem;">❌ เกิดข้อผิดพลาด</h3>';
            echo '<p style="color: #a9bcd0; margin-top: 15px;">ไม่สามารถบันทึกข้อมูลได้: ' . $conn->error . '</p>';
            echo '<a href="booking.php" class="btn" style="display: inline-block; margin-top: 25px; background: #2a4365; width: auto; padding: 10px 30px;">ลองใหม่อีกครั้ง</a>';
        }
    }

    echo '</div>';
    include '../includes/footer.php'; 
} else {
    // ถ้าไม่ได้เข้ามาผ่านการกดปุ่ม Submit ให้เด้งกลับไปหน้าฟอร์ม
    header("Location: booking.php");
    exit();
}
?>