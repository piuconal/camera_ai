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
    <!-- Thêm jQuery và Bootstrap (nếu chưa có) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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
                    <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePasswordModal">Đổi mật khẩu</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editProfileModal">Cập nhật thông tin</a></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Modal Đổi Mật Khẩu -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="changePasswordLabel">Đổi Mật Khẩu</h5>
        </div>
        <div class="modal-body">
            <form id="changePasswordForm">
            <div class="form-group">
                <label for="old_password">Mật khẩu cũ</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Nhập lại mật khẩu mới</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Cập Nhật</button>
            </form>
            <div id="passwordChangeMessage" class="mt-3"></div>
        </div>
        </div>
    </div>
    </div>
    <!-- Modal Chỉnh sửa thông tin -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Cập nhật thông tin cá nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                    <div id="updateMessage" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h3 class="text-center">Danh sách Camera</h3>
        <div class="text-end mb-3">
            <!-- Nút mở modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCameraModal">
                <i class="bi bi-plus-circle"></i> Thêm Camera
            </button>
        </div>
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm camera...">
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
            <tbody id="cameraTable">
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
                            <td class="camera-name"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="camera-area"><?= htmlspecialchars($row['area']) ?></td>
                            <td class="camera-description"><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <a href="camera_details.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-info btn-sm">Xem</a>
                                <button class="btn btn-warning btn-sm" onclick="showEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['area']) ?>', '<?= htmlspecialchars($row['description']) ?>')">Sửa</button>
                                <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?= $row['id'] ?>)">Xóa</button>
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

    <!-- Modal Sửa Camera -->
    <div class="modal fade" id="editCameraModal" tabindex="-1" aria-labelledby="editCameraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCameraModalLabel">Chỉnh sửa Camera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCameraForm" action="edit_camera.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="editCameraId" name="camera_id">
                        <div class="mb-3">
                            <label for="editCameraName" class="form-label">Tên Camera</label>
                            <input type="text" class="form-control" id="editCameraName" name="camera_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCameraArea" class="form-label">Khu vực</label>
                            <input type="text" class="form-control" id="editCameraArea" name="camera_area" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCameraDescription" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="editCameraDescription" name="camera_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận Xóa Camera -->
    <div class="modal fade" id="deleteCameraModal" tabindex="-1" aria-labelledby="deleteCameraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCameraModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa camera này không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                </div>
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
    <script>
        // Hiển thị modal sửa camera
        function showEditModal(id, name, area, description) {
            document.getElementById('editCameraId').value = id;
            document.getElementById('editCameraName').value = name;
            document.getElementById('editCameraArea').value = area;
            document.getElementById('editCameraDescription').value = description;
            var editModal = new bootstrap.Modal(document.getElementById('editCameraModal'));
            editModal.show();
        }

        // Hiển thị modal xác nhận xóa camera
        let deleteCameraId = null;
        function showDeleteModal(id) {
            deleteCameraId = id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteCameraModal'));
            deleteModal.show();
        }

        // Xác nhận xóa camera
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteCameraId) {
                fetch('delete_camera.php?id=' + deleteCameraId, {
                    method: 'GET'
                }).then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                }).catch(error => console.error(error));
            }
        });
    </script>
    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#cameraTable tr");

            rows.forEach(row => {
                let name = row.querySelector(".camera-name").textContent.toLowerCase();
                let area = row.querySelector(".camera-area").textContent.toLowerCase();
                let description = row.querySelector(".camera-description").textContent.toLowerCase();

                if (name.includes(filter) || area.includes(filter) || description.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#changePasswordForm").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "change_password.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        $("#passwordChangeMessage").html('<div class="alert alert-' + (response.status === "success" ? "success" : "danger") + '">' + response.message + '</div>');
                        if (response.status === "success") {
                            $("#changePasswordForm")[0].reset();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#editProfileModal').on('show.bs.modal', function() {
                $.ajax({
                    url: 'get_user_info.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#username').val(response.data.username);
                            $('#email').val(response.data.email);
                            $('#fullname').val(response.data.fullname);
                            $('#phone').val(response.data.phone);
                            $('#address').val(response.data.address); // Thêm địa chỉ
                        } else {
                            alert('Không thể lấy thông tin người dùng.');
                        }
                    }
                });
            });


            $('#editProfileForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'update_user_info.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#updateMessage').html('<div class="alert alert-' + (response.status === 'success' ? 'success' : 'danger') + '">' + response.message + '</div>');
                        if (response.status === 'success') {
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    }
                });
            });

        });
    </script>

</body>
</html>
