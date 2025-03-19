<?php
require_once '../db_connect.php'; // Kết nối database

// Lấy toàn bộ danh sách camera từ database
$sql = "SELECT c.id, c.name, c.area, c.description, c.status, 
               u.fullname AS owner, c.created_at 
        FROM cameras AS c
        JOIN users AS u ON c.user_id = u.id
        ORDER BY c.id DESC";

$result = $conn->query($sql);
?>

<h2></h2>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Tên Camera</th>
            <th>Khu vực</th>
            <th>Mô tả</th>
            <th>Chủ sở hữu</th>
            <th>Ngày tạo</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['area']) ?></td>
                <td><?= htmlspecialchars($row['description'] ?? 'Không có') ?></td>
                <td><?= htmlspecialchars($row['owner']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <?= $row['status'] ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Không hoạt động</span>' ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
