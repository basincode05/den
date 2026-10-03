<?php
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = dirname(__DIR__) . $request_uri;

if ($request_uri == '/' || $request_uri == '') {
    $file = dirname(__DIR__) . '/index.php';
} elseif (is_dir($file)) {
    $file = rtrim($file, '/') . '/index.php';
}

if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    // ถ้าระบุไฟล์ .php ตรงๆ ให้โหลดมาแสดง
    require $file;
} else if (file_exists($file) && is_file($file)) {
    // ถ้าเป็นรูปภาพ หรือ css ให้ return ไฟล์ไปตรงๆ
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mime_types = [
        'css' => 'text/css',
        'js'  => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg'=> 'image/jpeg',
        'gif' => 'image/gif'
    ];
    $mime = isset($mime_types[$ext]) ? $mime_types[$ext] : mime_content_type($file);
    header("Content-Type: $mime");
    readfile($file);
} else {
    http_response_code(404);
    echo "404 Not Found";
}
