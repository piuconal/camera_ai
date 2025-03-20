<?php
session_start();
include 'db_connect.php'; // Kết nối DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // ID người dùng đang đăng nhập
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Mật khẩu mới không khớp!"]);
        exit;
    }

    // Lấy mật khẩu cũ từ DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Kiểm tra mật khẩu cũ
    if (!password_verify($old_password, $hashed_password)) {
        echo json_encode(["status" => "error", "message" => "Mật khẩu cũ không đúng!"]);
        exit;
    }

    // Cập nhật mật khẩu mới
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_hashed_password, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Mật khẩu đã được cập nhật!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật!"]);
    }
    $stmt->close();
    $conn->close();
}
?>
