<?php
session_start();
if($_SESSION['role'] != 'Patient'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

// ดึงข้อมูลบริการ
$service_query = $conn->query("SELECT * FROM Service");
// ดึงข้อมูลทันตแพทย์ที่สถานะ Active
$dentist_query = $conn->query("SELECT * FROM Dentist WHERE Status = 'Active'");

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>จองนัดหมายออนไลน์ (Appointment Booking)</h2>
    <p>กรุณาเลือกบริการ ทันตแพทย์ และวันเวลาที่ต้องการ</p>
</div>

<div class="booking-container" style="max-width: 600px; margin: 0 auto; background: #152238; padding: 30px; border-radius: 10px; border: 1px solid #2a4365;">
    <form action="process_booking.php" method="POST">
        
        <div class="input-group">
            <label>1. เลือกบริการ</label>
            <select name="service_id" required style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365;">
                <option value="">-- กรุณาเลือกบริการ --</option>
                <?php while($row = $service_query->fetch_assoc()): ?>
                    <option value="<?php echo $row['Service_ID']; ?>">
                        <?php echo $row['Service_Name']; ?> (<?php echo $row['Price']; ?> บาท)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="input-group" style="margin-top: 15px;">
            <label>2. เลือกทันตแพทย์</label>
            <select name="dentist_id" required style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365;">
                <option value="">-- กรุณาเลือกทันตแพทย์ --</option>
                <?php while($row = $dentist_query->fetch_assoc()): ?>
                    <option value="<?php echo $row['Dentist_ID']; ?>">
                        <?php echo $row['Name']; ?> (<?php echo $row['Specialty']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="input-group" style="margin-top: 15px;">
            <label>3. เลือกวันที่</label>
            <input type="date" name="appt_date" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365; box-sizing: border-box;">
        </div>

        <div class="input-group" style="margin-top: 15px;">
            <label>4. เลือกช่วงเวลา (Time Slot)</label>
            <select name="time_slot" required style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365;">
                <option value="">-- กรุณาเลือกเวลา --</option>
                <option value="09:00:00">09:00 - 09:30</option>
                <option value="09:30:00">09:30 - 10:00</option>
                <option value="10:00:00">10:00 - 10:30</option>
                <option value="10:30:00">10:30 - 11:00</option>
                <option value="13:00:00">13:00 - 13:30</option>
                <option value="13:30:00">13:30 - 14:00</option>
            </select>
        </div>

        <button type="submit" class="btn" style="margin-top: 25px;">ตรวจสอบและยืนยันการจอง</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>