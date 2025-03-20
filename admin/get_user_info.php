<?php
session_start();
include '../db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, fullname, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['status' => 'success', 'data' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy thông tin.']);
}
?>
