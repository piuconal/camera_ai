<?php
require_once '../db_connect.php'; // Kết nối database

// Lấy danh sách người dùng từ database
$sql = "SELECT id, fullname, email, role, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="text-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i> Thêm Người Dùng
        </button>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th>Vai Trò</th>
                <th>Ngày Tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['role'] == '1' ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-primary">User</span>' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="showEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['fullname']) ?>', '<?= htmlspecialchars($row['email']) ?>', '<?= $row['role'] ?>')">Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?= $row['id'] ?>)">Xóa</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có người dùng nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Người Dùng -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Thêm Người Dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add_user.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và Tên</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai Trò</label>
                        <select class="form-control" id="role" name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Người Dùng -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa Người Dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" action="edit_user.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="mb-3">
                        <label for="editFullname" class="form-label">Họ và Tên</label>
                        <input type="text" class="form-control" id="editFullname" name="fullname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Vai Trò</label>
                        <select class="form-control" id="editRole" name="role">
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
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

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa người dùng này không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showEditModal(id, fullname, email, role) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editFullname').value = fullname;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    }

    let deleteUserId = null;
    function showDeleteModal(id) {
        deleteUserId = id;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteUserId) {
            fetch('delete_user.php?id=' + deleteUserId, {
                method: 'GET'
            }).then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            }).catch(error => console.error(error));
        }
    });
</script>
