<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// นับจำนวนสินค้า
$query = "SELECT COUNT(*) as total FROM products";
$stmt = $db->prepare($query);
$stmt->execute();
$totalProducts = $stmt->fetch()['total'];

// นับจำนวนผู้ใช้
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$stmt = $db->prepare($query);
$stmt->execute();
$totalUsers = $stmt->fetch()['total'];

// นับคำสั่งซื้อวันนี้
$query = "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$todayOrders = $stmt->fetch()['total'];

// ยอดขายวันนี้
$query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
          WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'";
$stmt = $db->prepare($query);
$stmt->execute();
$todaySales = $stmt->fetch()['total'];

// กิจกรรมล่าสุด
$query = "SELECT 'user' as type, name, email, created_at FROM users 
          WHERE role = 'user' 
          UNION ALL
          SELECT 'product' as type, name, '', created_at FROM products
          ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$activities = $stmt->fetchAll();

// สินค้าขายดี
$query = "SELECT p.name, COALESCE(SUM(oi.quantity), 0) as sold
          FROM products p
          LEFT JOIN order_items oi ON p.id = oi.product_id
          GROUP BY p.id, p.name
          ORDER BY sold DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$topProducts = $stmt->fetchAll();

$data = [
    'stats' => [
        'totalProducts' => $totalProducts,
        'totalUsers' => $totalUsers,
        'todayOrders' => $todayOrders,
        'todaySales' => $todaySales
    ],
    'activities' => $activities,
    'topProducts' => $topProducts
];

sendJSON($data);
?>