<?php
require_once '../db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Xóa thành công"]);
    } else {
        echo json_encode(["message" => "Lỗi khi xóa"]);
    }

    $stmt->close();
    $conn->close();
}
?>
