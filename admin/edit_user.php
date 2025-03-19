<?php
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ?, email = ?, address = ?, role = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssiii", $fullname, $phone, $email, $address, $role, $status, $user_id);

    if ($stmt->execute()) {
        header("Location: index.php?page=member.php");
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
