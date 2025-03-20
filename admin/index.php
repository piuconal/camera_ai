<?php
session_start();

// Kiểm tra quyền truy cập (chỉ admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

// Lấy giá trị 'page' từ URL (mặc định là dashboard.php)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Thêm jQuery và Bootstrap (nếu chưa có) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        body {
            padding-top: 56px; /* Đẩy nội dung xuống dưới tránh navbar */
        }
        .logo-img {
            height: 50px;
            width: auto;
            border-radius: 10px;
        }
        .sidebar {
            width: 250px;
            height: calc(100vh - 56px); /* Giảm chiều cao để tránh bị che bởi navbar */
            position: fixed;
            top: 56px; /* Đẩy xuống dưới tránh navbar */
            left: 0;
            background: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="https://static.vecteezy.com/system/resources/previews/025/885/740/original/animal-bear-logo-illustration-design-template-free-vector.jpg" 
                    alt="Logo" 
                    class="logo-img">
            </a>

            <div class="dropdown ms-auto">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePasswordModal">Đổi mật khẩu</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editProfileModal">Cập nhật thông tin</a></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php">Đăng xuất</a></li>
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
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="index.php?page=statistics.php"><i class="bi bi-bar-chart"></i> Thống kê</a>
        <a href="index.php?page=camera.php"><i class="bi bi-camera-video"></i> Danh sách camera</a>
        <a href="index.php?page=member.php"><i class="bi bi-people"></i> Danh sách người dùng</a>
    </div>

    <!-- Nội dung Admin -->
    <div class="content">
        <?php
            if (file_exists($page)) {
                include $page;
            } else {
                echo "<h2>404 - Trang không tồn tại</h2>";
            }
        ?>
    </div>
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
