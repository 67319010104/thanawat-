<?php
// ตั้งค่าเขตเวลา
date_default_timezone_set('Asia/Bangkok');

// ค่าคงที่ของระบบ
define('SITE_NAME', 'Admin Dashboard');
define('SITE_URL', 'http://localhost/admin_dashboard/');
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// การตั้งค่า Session
session_start();

// ฟังก์ชันตรวจสอบการเข้าสู่ระบบ
function checkLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}

// ฟังก์ชันป้องกัน XSS
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// ฟังก์ชันส่งข้อมูล JSON
function sendJSON($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>