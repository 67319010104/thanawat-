<?php
require_once 'config/config.php';
require_once 'config/database.php';

// ถ้าเข้าสู่ระบบแล้ว ให้ไปหน้า dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.html');
    exit();
}

$error = '';

if ($_POST) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, name, email, password FROM users WHERE email = :email AND role = 'admin' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            header('Location: index.html');
            exit();
        } else {
            $error = 'รหัสผ่านไม่ถูกต้อง';
        }
    } else {
        $error = 'ไม่พบผู้ใช้งาน';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600 mt-2">เข้าสู่ระบบเพื่อจัดการ</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">อีเมล</label>
                <input type="email" name="email" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="admin@example.com">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">รหัสผ่าน</label>
                <input type="password" name="password" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="••••••••">
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                เข้าสู่ระบบ
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>ข้อมูลทดสอบ:</p>
            <p>Email: admin@example.com</p>
            <p>Password: 123456</p>
        </div>
    </div>
</body>
</html>