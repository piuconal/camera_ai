<?php
require_once '../db_connect.php';

if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['area']) && isset($_POST['description']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $area = $_POST['area'];
    $description = $_POST['description'];
    $status = intval($_POST['status']);

    $stmt = $conn->prepare("UPDATE cameras SET name=?, area=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("sssii", $name, $area, $description, $status, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}
?>
