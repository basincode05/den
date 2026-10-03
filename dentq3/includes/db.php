<?php
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$dbname = getenv('DB_NAME') ?: "dentq_db";
$port = getenv('DB_PORT') ?: 3306;

// Aiven MySQL requires SSL connection. We use mysqli_init and real_connect.
$conn = mysqli_init();

$flags = 0;
// ถ้าไม่ได้รันบน localhost (เช่น รันบน Vercel) ให้เปิดใช้ SSL
if ($host !== "localhost") {
    $flags = MYSQLI_CLIENT_SSL;
}

$conn->real_connect($host, $user, $pass, $dbname, $port, NULL, $flags);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่าให้รองรับภาษาไทย
$conn->set_charset("utf8mb4");
?>