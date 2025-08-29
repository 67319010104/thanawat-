<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/config.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // ดึงสินค้าตาม ID
            $query = "SELECT p.*, c.name as category_name FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
            $product = $stmt->fetch();
            sendJSON($product);
        } else {
            // ดึงสินค้าทั้งหมด
            $query = "SELECT p.*, c.name as category_name FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      ORDER BY p.created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll();
            sendJSON($products);
        }
        break;
        
    case 'POST':
        // เพิ่มสินค้าใหม่
        $query = "INSERT INTO products (name, description, category_id, price, stock, status) 
                  VALUES (:name, :description, :category_id, :price, :stock, :status)";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $input['name']);
        $stmt->bindParam(':description', $input['description']);
        $stmt->bindParam(':category_id', $input['category_id']);
        $stmt->bindParam(':price', $input['price']);
        $stmt->bindParam(':stock', $input['stock']);
        $stmt->bindParam(':status', $input['status']);
        
        if($stmt->execute()) {
            sendJSON(['message' => 'เพิ่มสินค้าสำเร็จ', 'id' => $db->lastInsertId()]);
        } else {
            sendJSON(['message' => 'เกิดข้อผิดพลาด'], 500);
        }
        break;
        
    case 'PUT':
        // แก้ไขสินค้า
        $query = "UPDATE products SET name = :name, description = :description, 
                  category_id = :category_id, price = :price, stock = :stock, 
                  status = :status WHERE id = :id";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':id', $input['id']);
        $stmt->bindParam(':name', $input['name']);
        $stmt->bindParam(':description', $input['description']);
        $stmt->bindParam(':category_id', $input['category_id']);
        $stmt->bindParam(':price', $input['price']);
        $stmt->bindParam(':stock', $input['stock']);
        $stmt->bindParam(':status', $input['status']);
        
        if($stmt->execute()) {
            sendJSON(['message' => 'แก้ไขสินค้าสำเร็จ']);
        } else {
            sendJSON(['message' => 'เกิดข้อผิดพลาด'], 500);
        }
        break;
        
    case 'DELETE':
        // ลบสินค้า
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $input['id']);
        
        if($stmt->execute()) {
            sendJSON(['message' => 'ลบสินค้าสำเร็จ']);
        } else {
            sendJSON(['message' => 'เกิดข้อผิดพลาด'], 500);
        }
        break;
}
?>