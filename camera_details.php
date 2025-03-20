<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    echo "Không tìm thấy camera!";
    exit();
}

$camera_id = intval($_GET['id']);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Lọc ảnh theo tên nếu có tìm kiếm
$sql = "SELECT * FROM images WHERE camera_id = $camera_id";
if ($search !== '') {
    $sql .= " AND animal_name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h3>Hình ảnh từ Camera: <?= htmlspecialchars($camera['name']) ?></h3>
        
        <!-- Form tìm kiếm -->
        <form method="GET" class="d-flex mb-3">
            <input type="hidden" name="id" value="<?= $camera_id ?>">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">
                <img style="width: 40px; height: 40px;" src="https://png.pngtree.com/png-clipart/20190705/original/pngtree-businessman-looking-through-a-magnifying-glass-png-image_4191876.jpg" alt="">
            </button>
            <button type="button" class="btn" onclick="openCamera()">
                <img style="width: 40px; height: 40px;" src="https://th.bing.com/th/id/OIP.PKRF0Lc-rtSjSQiomAlnbgHaHa?rs=1&pid=ImgDetMain" alt="">
            </button>
        </form>

        <hr>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 image-card" id="image-<?= $row['id'] ?>">
                        <div class="card mb-3 position-relative">
                            <img src="<?= htmlspecialchars($row['information']) ?>" class="card-img-top" alt="Hình ảnh">
                            <button class="btn btn-sm position-absolute top-0 end-0 m-1" onclick="deleteImage(<?= $row['id'] ?>)">❌</button>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['animal_name']) ?></h5>
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

        function deleteImage(imageId) {
            if (confirm("Bạn có chắc chắn muốn xóa ảnh này không?")) {
                $.ajax({
                    url: 'delete_image.php',
                    type: 'POST',
                    data: { id: imageId },
                    success: function(response) {
                        if (response === "success") {
                            $("#image-" + imageId).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert("Xóa ảnh thất bại!");
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
