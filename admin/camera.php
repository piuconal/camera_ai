<?php
require_once '../db_connect.php'; // Kết nối database

$sql = "SELECT c.id, c.name, c.area, c.description, c.status, 
               u.fullname AS owner, u.phone, c.created_at 
        FROM cameras AS c
        JOIN users AS u ON c.user_id = u.id
        ORDER BY c.id DESC";

$result = $conn->query($sql);
?>

<h2></h2>
<input type="text" id="search-input" class="form-control mb-2" placeholder="Tìm kiếm camera...">

<table id="camera-table" class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th class="sortable">Tên Camera</th>
            <th class="sortable">Khu vực</th>
            <th class="sortable">Mô tả</th>
            <th class="sortable">Chủ sở hữu</th>
            <th class="sortable">Số điện thoại</th>
            <th class="sortable">Ngày tạo</th>
            <th class="sortable">Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['area']) ?></td>
                <td><?= htmlspecialchars($row['description'] ?? 'Không có') ?></td>
                <td><?= htmlspecialchars($row['owner']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td class="status-cell" data-id="<?= $row['id'] ?>" style="cursor: pointer;">
                    <span class="badge <?= $row['status'] ? 'bg-success' : 'bg-danger' ?>">
                        <?= $row['status'] ? 'Hoạt động' : 'Không hoạt động' ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-info btn-sm view-btn" data-id="<?= $row['id'] ?>">Xem</button>
                    <button class="btn btn-warning btn-sm edit-btn"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['name']) ?>"
                        data-area="<?= htmlspecialchars($row['area']) ?>"
                        data-description="<?= htmlspecialchars($row['description']) ?>"
                        data-status="<?= $row['status'] ?>">
                        Sửa
                    </button>
                    <button class="btn btn-danger btn-sm delete-btn"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['name']) ?>">
                        Xóa
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>



<!-- Modal Sửa -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa Camera</h5>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Tên Camera</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-area">Khu vực</label>
                        <input type="text" class="form-control" id="edit-area" name="area" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Mô tả</label>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Trạng thái</label>
                        <select class="form-control" id="edit-status" name="status">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa camera <strong id="delete-name"></strong> không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirm-delete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Hiển thị modal sửa
        $(".edit-btn").click(function () {
            $("#edit-id").val($(this).data("id"));
            $("#edit-name").val($(this).data("name"));
            $("#edit-area").val($(this).data("area"));
            $("#edit-description").val($(this).data("description"));
            $("#edit-status").val($(this).data("status"));
            $("#editModal").modal("show");
        });

        // Xử lý cập nhật
        $("#editForm").submit(function (e) {
            e.preventDefault();
            $.post("edit_camera.php", $(this).serialize(), function (response) {
                if (response === "success") location.reload();
                else alert("Cập nhật thất bại!");
            });
        });

        // Hiển thị modal xóa
        $(".delete-btn").click(function () {
            $("#delete-name").text($(this).data("name"));
            $("#confirm-delete").data("id", $(this).data("id"));
            $("#deleteModal").modal("show");
        });

        // Xóa camera
        $("#confirm-delete").click(function () {
            $.post("delete_camera.php", { id: $(this).data("id") }, function (response) {
                if (response === "success") location.reload();
                else alert("Xóa thất bại!");
            });
        });

        // Cập nhật trạng thái khi double-click
        $(".status-cell").dblclick(function () {
            var cell = $(this);
            var cameraId = cell.data("id");
            var newStatus = cell.find("span").hasClass("bg-success") ? 0 : 1;

            $.post("update_status.php", { id: cameraId, status: newStatus }, function (response) {
                if (response === "success") {
                    cell.find("span").toggleClass("bg-success bg-danger").text(newStatus ? "Hoạt động" : "Không hoạt động");
                } else {
                    alert("Lỗi khi cập nhật trạng thái!");
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Mở tab mới khi ấn nút "Xem"
        $(".view-btn").click(function () {
            var cameraId = $(this).data("id");
            window.open("../camera_details.php?id=" + cameraId, "_blank");
        });
    });

</script>

<script>
    $(document).ready(function () {
        // Tìm kiếm trong bảng theo cột 2 (Khu vực) và cột 3 (Mô tả)
        $("#search-input").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#camera-table tbody tr").filter(function () {
                var area = $(this).children("td").eq(1).text().toLowerCase();
                var description = $(this).children("td").eq(2).text().toLowerCase();
                $(this).toggle(area.indexOf(value) > -1 || description.indexOf(value) > -1);
            });
        });

        // Sắp xếp cột khi double-click
        $(".sortable").on("dblclick", function () {
            var table = $(this).parents("table").eq(0);
            var rows = table.find("tbody tr").toArray();
            var index = $(this).index();
            var ascending = $(this).hasClass("asc");

            rows.sort(function (a, b) {
                var A = $(a).children("td").eq(index).text().toLowerCase();
                var B = $(b).children("td").eq(index).text().toLowerCase();
                return ascending ? A.localeCompare(B) : B.localeCompare(A);
            });

            $(this).toggleClass("asc desc").css("color", "red").siblings().css("color", "");
            table.children("tbody").empty().append(rows);
        });
    });

</script>