<?php
include 'config.php';

// ดึงข้อมูลสินค้าทั้งหมด
function getProducts() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// เพิ่มสินค้าใหม่
function addProduct($name, $description, $price, $stock, $category_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO products (name, description, price, stock, category_id) 
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$name, $description, $price, $stock, $category_id]);
}

// อัพเดทสินค้า
function updateProduct($id, $name, $price, $stock, $status) {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE products 
        SET name = ?, price = ?, stock = ?, status = ? 
        WHERE id = ?
    ");
    return $stmt->execute([$name, $price, $stock, $status, $id]);
}
?>