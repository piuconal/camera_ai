<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['camera_id'];
    $name = $_POST['camera_name'];
    $area = $_POST['camera_area'];
    $description = $_POST['camera_description'];

    $stmt = $conn->prepare("UPDATE cameras SET name = ?, area = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $area, $description, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật!'); window.location.href = 'dashboard.php';</script>";
    }
}
?>
