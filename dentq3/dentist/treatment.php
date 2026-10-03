<?php
session_start();
// ตรวจสอบสิทธิ์ Dentist
if($_SESSION['role'] != 'Dentist'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

// --- ส่วนบันทึกข้อมูลลงฐานข้อมูล ---
if(isset($_POST['save_treatment'])) {
    $appt_id = (int)$_POST['appt_id'];
    $note = $conn->real_escape_string($_POST['note']);
    // จัดการค่าว่างของวันที่
    $next_date = !empty($_POST['next_date']) ? "'" . $conn->real_escape_string($_POST['next_date']) . "'" : "NULL";

    // 1. บันทึกข้อมูลลงตารางหลัก Treatment_Record
    $sql_record = "INSERT INTO Treatment_Record (Appointment_ID, Note, Next_Appt_Date) 
                   VALUES ($appt_id, '$note', $next_date)";
                   
    if($conn->query($sql_record) === TRUE) {
        $record_id = $conn->insert_id; // ดึง Record_ID ที่เพิ่งถูกสร้าง

        // 2. ลูปบันทึกข้อมูลรายฟันลงตาราง Treatment_Detail
        $teeth = $_POST['tooth_id'];
        $problems = $_POST['problem_id'];
        $treatments = $_POST['treatment_id'];

        for($i = 0; $i < count($teeth); $i++) {
            // เช็คว่ามีการเลือกฟัน ปัญหา และการรักษา ครบถ้วนในแถวนั้นๆ
            if(!empty($teeth[$i]) && !empty($problems[$i]) && !empty($treatments[$i])) {
                $t_id = (int)$teeth[$i];
                $p_id = (int)$problems[$i];
                $tm_id = (int)$treatments[$i];
                
                $conn->query("INSERT INTO Treatment_Detail (Record_ID, Tooth_ID, Problem_ID, Treatment_Method_ID) 
                              VALUES ($record_id, $t_id, $p_id, $tm_id)");
            }
        }

        // 3. อัปเดตสถานะการนัดหมายเป็น Done (เสร็จสิ้น)
        $conn->query("UPDATE Appointment SET Status = 'Done' WHERE Appointment_ID = $appt_id");
        
        // แจ้งเตือนและกลับไปหน้าตารางนัด
        echo "<script>alert('บันทึกประวัติการรักษาเสร็จสิ้น'); window.location.href='schedule.php';</script>";
        exit();
    }
}

// --- ส่วนดึงข้อมูลสำหรับแสดงผลบนฟอร์ม ---
$appt_id = isset($_GET['appt_id']) ? (int)$_GET['appt_id'] : 0;
if($appt_id == 0) {
    header("Location: schedule.php");
    exit();
}

// ดึงข้อมูลผู้ป่วยและบริการ
$sql_appt = "SELECT a.*, p.Name_Surname, s.Service_Name 
             FROM Appointment a 
             JOIN Patient p ON a.Patient_ID = p.Patient_ID 
             JOIN Service s ON a.Service_ID = s.Service_ID 
             WHERE a.Appointment_ID = $appt_id";
$appt_result = $conn->query($sql_appt);
$appt = $appt_result->fetch_assoc();

// ดึง Master Data เก็บใส่ Array เพื่อใช้วนลูปสร้าง Dropdown
$teeth_data = []; $res = $conn->query("SELECT * FROM Tooth"); while($r = $res->fetch_assoc()) $teeth_data[] = $r;
$prob_data = []; $res = $conn->query("SELECT * FROM Problem"); while($r = $res->fetch_assoc()) $prob_data[] = $r;
$treat_data = []; $res = $conn->query("SELECT * FROM Treatment_Method"); while($r = $res->fetch_assoc()) $treat_data[] = $r;

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>บันทึกการรักษารายฟัน (Tooth-level Treatment Record)</h2>
    <p>ผู้ป่วย: <span style="color:#48cae4; font-weight:bold;"><?php echo htmlspecialchars($appt['Name_Surname']); ?></span> | 
       บริการที่จอง: <span style="color:#48cae4; font-weight:bold;"><?php echo htmlspecialchars($appt['Service_Name']); ?></span></p>
</div>

<div class="booking-container" style="max-width: 800px; margin: 0 auto; background: #152238; padding: 30px; border-radius: 10px; border: 1px solid #2a4365;">
    <form action="treatment.php" method="POST">
        <input type="hidden" name="appt_id" value="<?php echo $appt_id; ?>">

        <h3 style="color: #48cae4; margin-bottom: 15px;">ระบุข้อมูลฟันที่มีปัญหา (ระบุได้สูงสุด 3 ซี่)</h3>
        
        <!-- สร้างฟอร์มให้กรอกได้ 3 แถว -->
        <?php for($i=1; $i<=3; $i++): ?>
            <div style="display: flex; gap: 10px; margin-bottom: 15px; background: #0b1320; padding: 15px; border-radius: 8px; border: 1px solid #1f3a5e;">
                <div style="flex: 1;">
                    <label style="color:#a9bcd0; font-size:0.9rem; display:block; margin-bottom:5px;">ระบุซี่ฟัน (11-48)</label>
                    <select name="tooth_id[]" style="width: 100%; padding: 8px; border-radius: 5px; background: #152238; color: white; border: 1px solid #2a4365;">
                        <option value="">- เลือกซี่ฟัน -</option>
                        <?php foreach($teeth_data as $t) echo "<option value='{$t['Tooth_ID']}'>ฟันซี่ {$t['Tooth_ID']}</option>"; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="color:#a9bcd0; font-size:0.9rem; display:block; margin-bottom:5px;">ปัญหาที่พบ</label>
                    <select name="problem_id[]" style="width: 100%; padding: 8px; border-radius: 5px; background: #152238; color: white; border: 1px solid #2a4365;">
                        <option value="">- เลือกปัญหา -</option>
                        <?php foreach($prob_data as $p) echo "<option value='{$p['Problem_ID']}'>{$p['Problem_Name']}</option>"; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="color:#a9bcd0; font-size:0.9rem; display:block; margin-bottom:5px;">วิธีการรักษา</label>
                    <select name="treatment_id[]" style="width: 100%; padding: 8px; border-radius: 5px; background: #152238; color: white; border: 1px solid #2a4365;">
                        <option value="">- เลือกวิธีรักษา -</option>
                        <?php foreach($treat_data as $tm) echo "<option value='{$tm['Treatment_Method_ID']}'>{$tm['Treatment_Name']}</option>"; ?>
                    </select>
                </div>
            </div>
        <?php endfor; ?>

        <div class="input-group" style="margin-top: 20px;">
            <label>หมายเหตุและรายละเอียดเพิ่มเติม</label>
            <textarea name="note" rows="3" placeholder="เช่น คนไข้มีอาการเสียวฟันบริเวณซี่ 36 แนะนำให้ใช้ยาสีฟันลดอาการเสียวฟัน..." style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365; box-sizing: border-box;"></textarea>
        </div>

        <div class="input-group" style="margin-top: 15px;">
            <label>กำหนดวันนัดหมายครั้งถัดไป (ถ้ามี)</label>
            <input type="date" name="next_date" min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border-radius: 5px; background: #0b1320; color: white; border: 1px solid #2a4365; box-sizing: border-box;">
        </div>

        <button type="submit" name="save_treatment" class="btn" style="margin-top: 25px; width: 100%;">บันทึกประวัติและเสร็จสิ้นการรักษา</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>