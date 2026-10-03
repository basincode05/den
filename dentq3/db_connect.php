<?php
$host = 'mysql-28fde106-basinluklem-d12f.k.aivencloud.com';
$port = '14308';
$db   = 'dentq_db';
$user = 'avnadmin';
$pass = 'AVNS_rG8oXCUpDnyLBjlAhVj'; 

// สิ่งสำคัญ: ต้องระบุตำแหน่งไฟล์ ca.pem ในตัวแปร $options
// สมมติว่าไฟล์ ca.pem อยู่ในโฟลเดอร์เดียวกันกับไฟล์นี้
$ca_cert = __DIR__ . '/ca.pem'; 

$options = [
    PDO::MYSQL_ATTR_SSL_CA => $ca_cert,             // เปิดใช้งาน SSL และอ้างอิงไฟล์ Certificate
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // อาจต้องตั้งค่าเป็น false หากรันใน Localhost แล้วมีปัญหา
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// สร้าง Connection String (DSN)
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    // สั่งเชื่อมต่อฐานข้อมูล
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "เชื่อมต่อฐานข้อมูล Aiven สำเร็จ!";
    
    // ดึงข้อมูลจากตาราง dentist
$stmt = $pdo->query("SELECT * FROM dentist");
$dentists = $stmt->fetchAll();

// แสดงผลข้อมูลดิบออกมาดู
echo "<pre>";
print_r($dentists);
echo "</pre>";

} catch (\PDOException $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อ: " . $e->getMessage();
}
?>