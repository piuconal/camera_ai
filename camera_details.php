<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    echo "Không tìm thấy camera!";
    exit();
}

$camera_id = intval($_GET['id']);

// Lấy danh sách ảnh từ camera
$sql = "SELECT * FROM images WHERE camera_id = $camera_id";
$result = $conn->query($sql);

// Lấy thông tin camera
$camera_sql = "SELECT * FROM cameras WHERE id = $camera_id";
$camera_result = $conn->query($camera_sql);
$camera = $camera_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hình ảnh từ Camera: <?= htmlspecialchars($camera['name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h3>Hình ảnh từ Camera: <?= htmlspecialchars($camera['name']) ?></h3>
        <button class="btn btn-primary" onclick="openCamera()">📷 Xem Camera</button>
        <hr>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['animal_name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($row['information']) ?></p>
                                <p class="text-muted">Thời gian: <?= $row['created_at'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">Không có hình ảnh nào từ camera này.</p>
        <?php endif; ?>
    </div>

    <script>
        function openCamera() {
            window.open('camera_live.php?id=<?= $camera_id ?>', '_blank');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
