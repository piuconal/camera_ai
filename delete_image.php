<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $image_id = intval($_POST['id']);
    $sql = "DELETE FROM images WHERE id = $image_id";
    
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}
?>
