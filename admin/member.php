<?php
require_once '../db_connect.php'; // Kết nối database

// Lấy danh sách người dùng từ database
$sql = "SELECT id, username, fullname, phone, email, address, role, status, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <input type="text" id="searchInput" class="form-control w-25" placeholder="Tìm kiếm theo tên hoặc sđt">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i> Thêm Người Dùng
        </button>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th ondblclick="sortTable(0, this)">Username</th>
                <th ondblclick="sortTable(1, this)">Họ và Tên</th>
                <th ondblclick="sortTable(2, this)">Số Điện Thoại</th>
                <th ondblclick="sortTable(3, this)">Email</th>
                <th ondblclick="sortTable(4, this)">Địa Chỉ</th>
                <th ondblclick="sortTable(5, this)">Vai Trò</th>
                <th ondblclick="sortTable(6, this)">Trạng Thái</th>
                <th ondblclick="sortTable(7, this)">Ngày Tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= $row['role'] == '1' ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-primary">User</span>' ?></td>
                        <td><?= $row['status'] == '1' ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-secondary">Bị khóa</span>' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                        <button class="btn btn-warning btn-sm" onclick='showEditModal(<?= json_encode($row['id']) ?>, <?= json_encode($row['username']) ?>, <?= json_encode($row['fullname']) ?>, <?= json_encode($row['phone']) ?>, <?= json_encode($row['email']) ?>, <?= json_encode($row['address']) ?>, <?= json_encode($row['role']) ?>, <?= json_encode($row['status']) ?>)'>Sửa</button>

                            <button class="btn btn-danger btn-sm" onclick="showDeleteModal(<?= $row['id'] ?>)">Xóa</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">Chưa có người dùng nào.</td>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Số Điện Thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Địa Chỉ</label>
                            <textarea class="form-control" id="address" name="address"></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="role" class="form-label">Vai Trò</label>
                            <select class="form-control" id="role" name="role">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Trạng Thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="0">Bị khóa</option>
                                <option value="1">Hoạt động</option>
                            </select>
                        </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editUsername" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editFullname" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="editFullname" name="fullname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPhone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editAddress" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="editAddress" name="address"></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="editRole" class="form-label">Vai Trò</label>
                            <select class="form-control" id="editRole" name="role">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="editStatus" class="form-label">Trạng thái</label>
                            <select class="form-control" id="editStatus" name="status">
                                <option value="0">Bị khóa</option>
                                <option value="1">Hoạt động</option>
                            </select>
                        </div>
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
    function showEditModal(id, username, fullname, phone, email, address, role, status) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editUsername').value = username;
        document.getElementById('editFullname').value = fullname;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editEmail').value = email;
        document.getElementById('editAddress').value = address;
        document.getElementById('editRole').value = role;
        document.getElementById('editStatus').value = status;
        
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const tableRows = document.querySelectorAll("tbody tr");

        function filterTable() {
            const searchValue = searchInput.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const fullname = row.cells[1].textContent.toLowerCase();
                const phone = row.cells[2].textContent.toLowerCase();

                if (fullname.includes(searchValue) || phone.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        searchInput.addEventListener("input", filterTable);
    });

</script>

<script>
    let sortDirection = {}; // Để lưu trạng thái sắp xếp từng cột

    function sortTable(columnIndex, header) {
        const table = document.querySelector("table tbody");
        const rows = Array.from(table.querySelectorAll("tr"));
        const isAscending = !sortDirection[columnIndex];

        // Đổi màu đỏ cho tiêu đề đang được chọn
        document.querySelectorAll("thead th").forEach(th => th.style.color = ""); 
        header.style.color = "red";

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim();
            const bText = b.cells[columnIndex].textContent.trim();
            
            return isAscending ? aText.localeCompare(bText, 'vi', { numeric: true }) : bText.localeCompare(aText, 'vi', { numeric: true });
        });

        // Cập nhật trạng thái sắp xếp
        sortDirection[columnIndex] = isAscending;

        // Thêm lại các dòng đã sắp xếp vào bảng
        table.innerHTML = "";
        rows.forEach(row => table.appendChild(row));
    }

</script>