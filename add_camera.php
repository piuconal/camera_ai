<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['camera_name']);
    $area = trim($_POST['camera_area']);
    $description = trim($_POST['camera_description']);

    // Kiểm tra tên camera có trùng không
    $check_sql = "SELECT * FROM cameras WHERE name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Tên camera đã tồn tại!'); window.history.back();</script>";
        exit();
    }

    // Thêm camera mới
    $sql = "INSERT INTO cameras (user_id, name, area, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $name, $area, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm camera thành công!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi thêm camera!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
