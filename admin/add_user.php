<?php
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $role = 0; // Mặc định là User
    $status = 0; // Mặc định là chưa kích hoạt

    $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, phone, email, address, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssii", $username, $password, $fullname, $phone, $email, $address, $role, $status);

    if ($stmt->execute()) {
        header("Location: index.php?page=member.php");
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
