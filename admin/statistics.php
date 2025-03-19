<?php
require_once '../db_connect.php'; // Kết nối database

// Lấy số lượng camera
$result = $conn->query("SELECT COUNT(*) AS total FROM cameras");
$total_cameras = $result->fetch_assoc()['total'];

// Lấy số lượng ảnh
$result = $conn->query("SELECT COUNT(*) AS total FROM images");
$total_images = $result->fetch_assoc()['total'];

// Lấy số lượng người dùng
$result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_users = $result->fetch_assoc()['total'];

// Lấy số lượng loài động vật khác nhau
$result = $conn->query("SELECT COUNT(DISTINCT animal_name) AS total FROM images");
$total_animals = $result->fetch_assoc()['total'];
?>

<h2></h2>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Tổng số Camera</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_cameras ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Tổng số ảnh</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_images ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Tổng số người dùng</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_users ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Số loài động vật nhận diện</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_animals ?></h4>
            </div>
        </div>
    </div>
</div>
