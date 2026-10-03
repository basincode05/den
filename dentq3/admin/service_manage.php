<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if($_SESSION['role'] != 'Admin'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

// --- 1. ส่วนจัดการการเพิ่มข้อมูล (Create) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];

    $sql = "INSERT INTO Service (Service_Name, Description, Duration, Price) 
            VALUES ('$service_name', '$description', $duration, $price)";
    
    if ($conn->query($sql) === TRUE) {
        $success_msg = "เพิ่มบริการสำเร็จเรียบร้อย";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// --- 2. ส่วนจัดการการแก้ไขข้อมูล (Update) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $id = (int)$_POST['service_id'];
    $service_name = $conn->real_escape_string($_POST['service_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];

    $sql_update = "UPDATE Service SET 
                    Service_Name = '$service_name', 
                    Description = '$description', 
                    Duration = $duration, 
                    Price = $price
                   WHERE Service_ID = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        $success_msg = "อัปเดตข้อมูลบริการสำเร็จ";
    } else {
        $error_msg = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// --- 3. ส่วนจัดการการลบข้อมูล (Delete) ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM Service WHERE Service_ID = $delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        $success_msg = "ลบบริการเรียบร้อยแล้ว";
    }
}

// --- 4. เตรียมข้อมูลสำหรับฟอร์มแก้ไข ---
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res_edit = $conn->query("SELECT * FROM Service WHERE Service_ID = $edit_id");
    if ($res_edit->num_rows > 0) {
        $edit_data = $res_edit->fetch_assoc();
    }
}

// --- 5. ดึงข้อมูลบริการทั้งหมดมาแสดง (Read) ---
$result = $conn->query("SELECT * FROM Service ORDER BY Service_ID DESC");

include '../includes/header.php'; 
?>

<div class="dashboard-header">
    <h2>จัดการข้อมูลบริการ (Manage Services)</h2>
    <p>เพิ่ม แก้ไข ลบ และตรวจสอบรายการบริการของคลินิก</p>
</div>

<div style="display: flex; gap: 20px; align-items: flex-start; max-width: 1100px; margin: 0 auto;">
    
    <!-- ฝั่งซ้าย: ฟอร์มเพิ่ม/แก้ไขบริการ -->
    <div class="booking-container" style="flex: 1;">
        <?php if($edit_data): ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="edit"></i> แก้ไขข้อมูลบริการ</h3>
        <?php else: ?>
            <h3 style="color: var(--primary); margin-bottom: 20px;"><i data-lucide="plus-circle"></i> เพิ่มบริการใหม่</h3>
        <?php endif; ?>
        
        <?php if(isset($success_msg)) echo "<p style='color: #10b981; margin-bottom: 15px; background: #f0fdf4; padding: 10px; border-radius: 8px;'>$success_msg</p>"; ?>
        <?php if(isset($error_msg)) echo "<p style='color: #ef4444; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-radius: 8px;'>$error_msg</p>"; ?>

        <form action="service_manage.php" method="POST">
            <?php if($edit_data): ?>
                <input type="hidden" name="service_id" value="<?php echo $edit_data['Service_ID']; ?>">
            <?php endif; ?>

            <div class="input-group">
                <label>ชื่อบริการ</label>
                <input type="text" name="service_name" required placeholder="เช่น ขูดหินปูน, อุดฟัน" value="<?php echo $edit_data ? htmlspecialchars($edit_data['Service_Name']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>รายละเอียด</label>
                <textarea name="description" rows="3" required placeholder="อธิบายบริการสั้นๆ..."><?php echo $edit_data ? htmlspecialchars($edit_data['Description']) : ''; ?></textarea>
            </div>
            <div class="input-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>ระยะเวลา (นาที)</label>
                    <input type="number" name="duration" required placeholder="เช่น 30" value="<?php echo $edit_data ? $edit_data['Duration'] : ''; ?>">
                </div>
                <div style="flex: 1;">
                    <label>ราคา (บาท)</label>
                    <input type="number" name="price" step="0.01" required placeholder="เช่น 500" value="<?php echo $edit_data ? $edit_data['Price'] : ''; ?>">
                </div>
            </div>

            <?php if($edit_data): ?>
                <button type="submit" name="update_service" class="btn" style="margin-top: 15px; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">บันทึกการแก้ไข</button>
                <a href="service_manage.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-weight: 500;">ยกเลิกการแก้ไข</a>
            <?php else: ?>
                <button type="submit" name="add_service" class="btn" style="margin-top: 15px;">บันทึกข้อมูล</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- ฝั่งขวา: ตารางแสดงบริการ -->
    <div class="booking-container" style="flex: 1.5;">
        <h3 style="color: var(--secondary); margin-bottom: 20px;"><i data-lucide="list"></i> รายการบริการปัจจุบัน</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; color: var(--text-muted);">
                    <th style="padding: 10px;">ชื่อบริการ</th>
                    <th style="padding: 10px;">เวลา</th>
                    <th style="padding: 10px;">ราคา</th>
                    <th style="padding: 10px; text-align: center;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px; font-weight: 500;"><?php echo htmlspecialchars($row['Service_Name']); ?></td>
                            <td style="padding: 10px;"><?php echo $row['Duration']; ?> นาที</td>
                            <td style="padding: 10px;"><?php echo number_format($row['Price'], 0); ?> ฿</td>
                            <td style="padding: 10px;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <a href="service_manage.php?edit_id=<?php echo $row['Service_ID']; ?>" 
                                       title="แก้ไขข้อมูล"
                                       style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; box-shadow: 0 2px 4px rgba(59,130,246,0.05);">
                                        <i data-lucide="edit" style="width: 14px; height: 14px; margin-right: 5px;"></i> แก้ไข
                                    </a>
                                    <a href="service_manage.php?delete_id=<?php echo $row['Service_ID']; ?>" 
                                       onclick="return confirm('ยืนยันการลบบริการนี้?');" 
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
                        <td colspan="4" style="padding: 15px; text-align: center; color: var(--text-muted);">ยังไม่มีข้อมูลบริการ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>