<?php
include 'config.php';

// ดึงข้อมูลผู้ใช้ทั้งหมด
function getUsers() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT * FROM users 
        WHERE role = 'customer' 
        ORDER BY created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// เพิ่มผู้ใช้ใหม่
function addUser($name, $email, $phone, $password) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, phone, password) 
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$name, $email, $phone, $hashedPassword]);
}

// อัพเดทสถานะผู้ใช้
function updateUserStatus($id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}
?>


<?php
include 'config.php';
include 'products.php';
include 'users.php';

// ดึงสถิติต่างๆ
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$todayOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$todaySales = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$products = getProducts();
$users = getUsers();
?>