<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    $error_message = "Không tìm thấy camera!";
} else {
    $camera_id = intval($_GET['id']);
    $sql = "SELECT status FROM cameras WHERE id = $camera_id";
    $result = $conn->query($sql);
    $camera = $result->fetch_assoc();

    if (!$camera || $camera['status'] == 0) {
        $error_message = "Camera này đang bị vô hiệu hóa!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera trực tiếp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Lỗi!</strong> <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php else: ?>
            <h3>Camera Trực Tiếp</h3>
            <img id="video" src="http://localhost:5000/video_feed" width="800" height="500">
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!isset($error_message)): ?>
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                document.getElementById('video').srcObject = stream;
            } catch (error) {
                alert("Không thể mở camera: " + error.message);
            }
        }
        startCamera();
        <?php endif; ?>
    </script>
</body>
</html>
