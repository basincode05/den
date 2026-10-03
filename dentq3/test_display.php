<?php
// 1. นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db_connect.php';

try {
    // 2. เขียนคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง dentist
    $sql = "SELECT * FROM dentist";
    $stmt = $pdo->query($sql);
    
    // 3. ดึงข้อมูลทั้งหมดเก็บไว้ในตัวแปร $dentists
    $dentists = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อทันตแพทย์ - DentQ</title>
    <style>
        table { width: 60%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>รายชื่อทันตแพทย์ในคลินิก DentQ</h2>
    
    <table>
        <thead>
            <tr>
                <th>รหัสแพทย์</th>
                <th>ชื่อ-นามสกุล</th>
                <th>ความเชี่ยวชาญ</th>
                <th>เบอร์โทรศัพท์</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // 4. วนลูปข้อมูล $dentists เพื่อสร้างแถวตาราง <tr> ตามจำนวนข้อมูลที่มี
            if (!empty($dentists)) {
                foreach ($dentists as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['Dentist_ID'] . "</td>";
                    echo "<td>" . $row['Name'] . "</td>";
                    echo "<td>" . $row['Specialty'] . "</td>";
                    echo "<td>" . $row['Phone'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>ไม่พบข้อมูลทันตแพทย์</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>