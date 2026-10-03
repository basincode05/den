<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if($_SESSION['role'] != 'Admin'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

// --- 1. ส่วนจัดการการเพิ่มข้อมูล (Create) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_dentist'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "INSERT INTO Dentist (Name, Specialty, Phone, Email, Password, Status) 
            VALUES ('$name', '$specialty', '$phone', '$email', '$password', 'Active')";
    
    if ($conn->query($sql) === TRUE) {
        $success_msg = "เพิ่มรายชื่อทันตแพทย์สำเร็จ";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// --- 2. ส่วนจัดการการแก้ไขข้อมูล (Update) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_dentist'])) {
    $id = (int)$_POST['dentist_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql_update = "UPDATE Dentist SET 
                    Name = '$name', 
                    Specialty = '$specialty', 
                    Phone = '$phone', 
                    Email = '$email', 
                    Password = '$password',
                    Status = '$status'
                   WHERE Dentist_ID = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        $success_msg = "อัปเดตข้อมูลทันตแพทย์สำเร็จ";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// --- 3. ส่วนจัดการการลบข้อมูล (Delete) ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM Dentist WHERE Dentist_ID = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        $success_msg = "ลบข้อมูลทันตแพทย์เรียบร้อยแล้ว";
    }
}

// --- 4. เตรียมข้อมูลสำหรับฟอร์มแก้ไข (ถ้ามีการกดปุ่มแก้ไขมา) ---
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res_edit = $conn->query("SELECT * FROM Dentist WHERE Dentist_ID = $edit_id");
    if ($res_edit->num_rows > 0) {
        $edit_data = $res_edit->fetch_assoc();
    }
}

// --- 5. ดึงข้อมูลทันตแพทย์ทั้งหมดมาแสดง (Read) ---
$result = $conn->query("SELECT * FROM Dentist ORDER BY Dentist_ID DESC");

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>จัดการข้อมูลทันตแพทย์ (Manage Dentists)</h2>
    <p>เพิ่ม แก้ไขรายชื่อ ความเชี่ยวชาญ และรหัสเข้าใช้งานของทันตแพทย์</p>
</div>

<div style="display: flex; gap: 20px; align-items: flex-start; max-width: 1100px; margin: 0 auto;">
    
    <!-- ฝั่งซ้าย: ฟอร์มเพิ่ม/แก้ไขทันตแพทย์ -->
    <div class="booking-container" style="flex: 1;">
        <!-- เช็คว่าอยู่ในโหมดแก้ไขหรือไม่ เพื่อเปลี่ยนหัวข้อ -->
        <?php if($edit_data): ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="edit"></i> แก้ไขข้อมูลทันตแพทย์</h3>
        <?php else: ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="plus-circle"></i> เพิ่มทันตแพทย์ใหม่</h3>
        <?php endif; ?>
        
        <?php if(isset($success_msg)) echo "<p style='color: #10b981; margin-bottom: 15px; background: #f0fdf4; padding: 10px; border-radius: 8px;'>$success_msg</p>"; ?>
        <?php if(isset($error_msg)) echo "<p style='color: #ef4444; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-radius: 8px;'>$error_msg</p>"; ?>

        <form action="dentist_manage.php" method="POST">
            <!-- ถ้าแก้ไข ให้ส่ง ID ที่ซ่อนไว้ไปด้วย -->
            <?php if($edit_data): ?>
                <input type="hidden" name="dentist_id" value="<?php echo $edit_data['Dentist_ID']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label>ชื่อ-นามสกุล (พร้อมคำนำหน้า)</label>
                <!-- value= นำข้อมูลเดิมมาใส่ในช่องกรอก -->
                <input type="text" name="name" required placeholder="เช่น ทพ. สมชาย ใจดี" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Name']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label>ความเชี่ยวชาญเฉพาะทาง</label>
                <!-- นำโค้ด style ที่ทำให้สีพื้นหลังทึบออก เพื่อให้โปร่งใสตาม CSS หลัก -->
                <select name="specialty" required style="width: 100%; padding: 16px 20px; border-radius: 16px; border: 2px solid transparent; background: rgba(255, 255, 255, 0.6); font-family: inherit; font-size: 1.05rem; color: var(--text-main); font-weight: 500;">
                    <option value="ทันตกรรมทั่วไป" <?php echo ($edit_data && $edit_data['Specialty'] == 'ทันตกรรมทั่วไป') ? 'selected' : ''; ?>>ทันตกรรมทั่วไป</option>
                    <option value="จัดฟัน" <?php echo ($edit_data && $edit_data['Specialty'] == 'จัดฟัน') ? 'selected' : ''; ?>>จัดฟัน</option>
                    <option value="รักษารากฟัน" <?php echo ($edit_data && $edit_data['Specialty'] == 'รักษารากฟัน') ? 'selected' : ''; ?>>รักษารากฟัน</option>
                    <option value="ศัลยกรรมช่องปาก" <?php echo ($edit_data && $edit_data['Specialty'] == 'ศัลยกรรมช่องปาก') ? 'selected' : ''; ?>>ศัลยกรรมช่องปาก</option>
                </select>
            </div>
            
            <div class="input-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="text" name="phone" required placeholder="08X-XXX-XXXX" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Phone']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label>อีเมล (สำหรับใช้ Login)</label>
                <input type="email" name="email" required placeholder="doctor@clinic.com" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Email']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label>รหัสผ่าน (สำหรับใช้ Login)</label>
                <input type="text" name="password" required placeholder="กำหนดรหัสผ่าน" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Password']) : ''; ?>">
            </div>
            
            <!-- เพิ่มช่องให้แอดมินปรับสถานะหมอได้ กรณีลาออกหรือพักงาน (เฉพาะตอนกดแก้ไข) -->
            <?php if($edit_data): ?>
            <div class="input-group">
                <label>สถานะ</label>
                <select name="status" required style="width: 100%; padding: 16px 20px; border-radius: 16px; border: 2px solid transparent; background: rgba(255, 255, 255, 0.6); font-family: inherit; font-size: 1.05rem; color: var(--text-main); font-weight: 500;">
                    <option value="Active" <?php echo ($edit_data['Status'] == 'Active') ? 'selected' : ''; ?>>Active (ใช้งานปกติ)</option>
                    <option value="Inactive" <?php echo ($edit_data['Status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (ระงับการใช้งาน)</option>
                </select>
            </div>
            <?php endif; ?>

            <!-- เปลี่ยนปุ่มตามโหมด -->
            <?php if($edit_data): ?>
                <button type="submit" name="update_dentist" class="btn" style="margin-top: 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">บันทึกการแก้ไข</button>
                <a href="dentist_manage.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-weight: 500;">ยกเลิกการแก้ไข</a>
            <?php else: ?>
                <button type="submit" name="add_dentist" class="btn" style="margin-top: 15px;">เพิ่มข้อมูล</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- ฝั่งขวา: ตารางแสดงทันตแพทย์ -->
    <div class="booking-container" style="flex: 1.5;">
        <h3 style="color: var(--secondary); margin-bottom: 20px;"><i data-lucide="users"></i> รายชื่อทันตแพทย์ปัจจุบัน</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; color: var(--text-muted);">
                    <th style="padding: 10px;">ชื่อ-นามสกุล</th>
                    <th style="padding: 10px;">ความเชี่ยวชาญ</th>
                    <th style="padding: 10px;">เบอร์โทร</th>
                    <th style="padding: 10px;">สถานะ</th>
                    <th style="padding: 10px; text-align: center;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Specialty']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Phone']); ?></td>
                            <td style="padding: 10px;">
                                <!-- ป้ายสีเขียวสำหรับ Active และสีแดงสำหรับ Inactive -->
                                <span style="background: <?php echo ($row['Status'] == 'Active') ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo ($row['Status'] == 'Active') ? '#059669' : '#dc2626'; ?>; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                    <?php echo $row['Status']; ?>
                                </span>
                            </td>
                            <td style="padding: 10px;">
    <div style="display: flex; justify-content: center; gap: 8px;">
        <!-- ปุ่มแก้ไข (สีฟ้า) -->
        <a href="dentist_manage.php?edit_id=<?php echo $row['Dentist_ID']; ?>" 
           title="แก้ไขข้อมูล"
           style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; box-shadow: 0 2px 4px rgba(59,130,246,0.05);">
            <i data-lucide="edit" style="width: 14px; height: 14px; margin-right: 5px;"></i> แก้ไข
        </a>
        
        <!-- ปุ่มลบ (สีแดง) -->
        <a href="dentist_manage.php?delete_id=<?php echo $row['Dentist_ID']; ?>" 
           onclick="return confirm('ยืนยันการลบรายชื่อทันตแพทย์ท่านนี้?');" 
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
                        <td colspan="5" style="padding: 15px; text-align: center; color: var(--text-muted);">ยังไม่มีข้อมูลทันตแพทย์</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>