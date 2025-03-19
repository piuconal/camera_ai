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
                <li><a class="dropdown-item" href="change_password.php">Đổi mật khẩu</a></li>
                <li><a class="dropdown-item" href="edit_profile.php">Cập nhật thông tin</a></li>
                <li><a class="dropdown-item text-danger" href="../logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</nav>

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

</body>
</html>
