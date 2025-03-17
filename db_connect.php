<?php
$host = "localhost";
$username = "root"; // Thay bằng username của MySQL nếu khác
$password = ""; // Thay bằng mật khẩu nếu có
$database = "camera_ai";

// Kết nối database
$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
