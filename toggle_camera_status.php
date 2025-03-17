<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
    exit();
}

$camera_id = intval($data['id']);
$new_status = intval($data['status']);

$sql = "UPDATE cameras SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $new_status, $camera_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Cập nhật thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Cập nhật thất bại!"]);
}

$stmt->close();
$conn->close();
?>
