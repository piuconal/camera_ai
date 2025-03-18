<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM cameras WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Xóa thành công!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi xóa!"]);
    }
}
?>
