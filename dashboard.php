<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

// Lấy danh sách camera của user hiện tại
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM cameras WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .logo-img {
            height: 50px;
            width: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://static.vecteezy.com/system/resources/previews/025/885/740/original/animal-bear-logo-illustration-design-template-free-vector.jpg" 
                    alt="Logo" 
                    class="logo-img">
            </a>

            <div class="dropdown ms-auto">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> <?= $_SESSION['username']; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item" href="change_password.php">Đổi mật khẩu</a></li>
                    <li><a class="dropdown-item" href="edit_profile.php">Cập nhật thông tin</a></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="text-center">Danh sách Camera</h3>
        <div class="text-end mb-3">
            <!-- Nút mở modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCameraModal">
                <i class="bi bi-plus-circle"></i> Thêm Camera
            </button>
        </div>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Trạng Thái</th>
                    <th>Tên Camera</th>
                    <th>Khu vực</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td style="width: 150px;">
                                <button class="btn btn-sm" onclick="toggleStatus(<?= $row['id'] ?>, <?= $row['status'] ?>)">
                                    <?= ($row['status'] == 1) ? '🟢' : '🔴' ?>
                                </button>
                                <?php if ($row['status'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Đã tắt</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <a href="camera_details.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-info btn-sm">Xem</a>
                                <a href="edit_camera.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete_camera.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa camera này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Chưa có camera nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Thêm Camera -->
    <div class="modal fade" id="addCameraModal" tabindex="-1" aria-labelledby="addCameraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCameraModalLabel">Thêm Camera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_camera.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cameraName" class="form-label">Tên Camera</label>
                            <input type="text" class="form-control" id="cameraName" name="camera_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="cameraArea" class="form-label">Khu vực</label>
                            <input type="text" class="form-control" id="cameraArea" name="camera_area" required>
                        </div>
                        <div class="mb-3">
                            <label for="cameraDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="cameraDescription" name="camera_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu Camera</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStatus(cameraId, currentStatus) {
            let newStatus = currentStatus === 1 ? 0 : 1;
            
            fetch('toggle_camera_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: cameraId, status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Có lỗi xảy ra!");
                }
            });
        }
    </script>

</body>
</html>
