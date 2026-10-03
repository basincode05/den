<?php
session_start();
if($_SESSION['role'] != 'Admin'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

// --- 1. ส่วนจัดการการเพิ่มข้อมูล (Create) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_patient'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string($_POST['password']);
    
    // เช็คอีเมลซ้ำ
    $check = $conn->query("SELECT Email FROM Patient WHERE Email = '$email'");
    if($check->num_rows > 0){
        $error_msg = "อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น";
    } else {
        $sql = "INSERT INTO Patient (Name_Surname, Phone, Email, Password) 
                VALUES ('$name', '$phone', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $success_msg = "เพิ่มประวัติผู้ป่วยใหม่เรียบร้อยแล้ว";
        } else {
            $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}

// --- 2. ส่วนจัดการการแก้ไขข้อมูล (Update) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_patient'])) {
    $id = (int)$_POST['patient_id'];
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string($_POST['password']);

    $sql_update = "UPDATE Patient SET 
                    Name_Surname = '$name', 
                    Phone = '$phone', 
                    Email = '$email', 
                    Password = '$password'
                   WHERE Patient_ID = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        $success_msg = "อัปเดตข้อมูลผู้ป่วยสำเร็จ";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// --- 3. ส่วนจัดการการลบข้อมูล (Delete) ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // ตรวจสอบ Foreign Key ป้องกันการลบคนไข้ที่มีประวัติ
    $check_sql = "SELECT * FROM Appointment WHERE Patient_ID = $delete_id";
    $check_res = $conn->query($check_sql);
    
    if($check_res->num_rows > 0) {
        $error_msg = "ไม่สามารถลบข้อมูลผู้ป่วยได้ เนื่องจากมีประวัติการทำรายการในระบบ (เพื่อป้องกันข้อมูลสูญหาย)";
    } else {
        $sql_delete = "DELETE FROM Patient WHERE Patient_ID = $delete_id";
        if ($conn->query($sql_delete) === TRUE) {
            $success_msg = "ลบข้อมูลผู้ป่วยเรียบร้อยแล้ว";
        } else {
            $error_msg = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error;
        }
    }
}

// --- 4. เตรียมข้อมูลสำหรับฟอร์มแก้ไข ---
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res_edit = $conn->query("SELECT * FROM Patient WHERE Patient_ID = $edit_id");
    if ($res_edit->num_rows > 0) {
        $edit_data = $res_edit->fetch_assoc();
    }
}

// --- 5. ดึงข้อมูลผู้ป่วยทั้งหมดมาแสดง ---
$result = $conn->query("SELECT * FROM Patient ORDER BY Patient_ID DESC");

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>จัดการข้อมูลผู้ป่วย (Manage Patients)</h2>
    <p>เพิ่ม แก้ไข และตรวจสอบรายชื่อผู้ป่วยในคลินิก</p>
</div>

<div style="display: flex; gap: 20px; align-items: flex-start; max-width: 1100px; margin: 0 auto;">
    
    <!-- ฝั่งซ้าย: ฟอร์มเพิ่ม/แก้ไขคนไข้ -->
    <div class="booking-container" style="flex: 1;">
        <?php if($edit_data): ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="user-pen"></i> แก้ไขข้อมูลผู้ป่วย</h3>
        <?php else: ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="user-plus"></i> เพิ่มประวัติผู้ป่วย</h3>
        <?php endif; ?>
        
        <?php if(isset($success_msg)) echo "<p style='color: #10b981; margin-bottom: 15px; background: #f0fdf4; padding: 10px; border-radius: 8px;'>$success_msg</p>"; ?>
        <?php if(isset($error_msg)) echo "<p style='color: #ef4444; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-radius: 8px;'>$error_msg</p>"; ?>

        <form action="patient_manage.php" method="POST">
            <?php if($edit_data): ?>
                <input type="hidden" name="patient_id" value="<?php echo $edit_data['Patient_ID']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" name="name" required placeholder="เช่น สมหญิง รักฟันสวย" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Name_Surname']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="text" name="phone" required placeholder="08X-XXX-XXXX" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Phone']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>อีเมล (สำหรับใช้ Login)</label>
                <input type="email" name="email" required placeholder="example@email.com" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Email']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>รหัสผ่าน</label>
                <input type="text" name="password" required placeholder="กำหนดรหัสผ่าน" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Password']) : ''; ?>">
            </div>

            <?php if($edit_data): ?>
                <button type="submit" name="update_patient" class="btn" style="margin-top: 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">บันทึกการแก้ไข</button>
                <a href="patient_manage.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-weight: 500;">ยกเลิกการแก้ไข</a>
            <?php else: ?>
                <button type="submit" name="add_patient" class="btn" style="margin-top: 15px;">เพิ่มข้อมูล</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- ฝั่งขวา: ตารางแสดงผู้ป่วย -->
    <div class="booking-container" style="flex: 1.5;">
        <h3 style="color: var(--secondary); margin-bottom: 20px;"><i data-lucide="users"></i> รายชื่อผู้ป่วยทั้งหมด</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; color: var(--text-muted);">
                    <th style="padding: 10px;">รหัส</th>
                    <th style="padding: 10px;">ชื่อ-นามสกุล</th>
                    <th style="padding: 10px;">เบอร์โทร</th>
                    <th style="padding: 10px;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px; font-weight: 600; color: var(--primary);">PID-<?php echo str_pad($row['Patient_ID'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Name_Surname']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Phone']); ?></td>
                            <td style="padding: 10px;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <a href="patient_manage.php?edit_id=<?php echo $row['Patient_ID']; ?>" 
                                       title="แก้ไขข้อมูล"
                                       style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; box-shadow: 0 2px 4px rgba(59,130,246,0.05);">
                                        <i data-lucide="edit" style="width: 14px; height: 14px; margin-right: 5px;"></i> แก้ไข
                                    </a>
                                    <a href="patient_manage.php?delete_id=<?php echo $row['Patient_ID']; ?>" 
                                       onclick="return confirm('ยืนยันการลบข้อมูลผู้ป่วยรายนี้?');" 
                                       title="ลบข้อมูล"
                                       style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; box-shadow: 0 2px 4px rgba(239,68,68,0.05);">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px; margin-right: 5px;"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="padding: 15px; text-align: center; color: var(--text-muted);">ยังไม่มีข้อมูลผู้ป่วยในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>