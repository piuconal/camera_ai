<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($user['status'] == 0) {
            echo json_encode(["status" => "error", "message" => "Tài khoản của bạn đã bị khóa!"]);
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo json_encode(["status" => "success", "redirect" => "dashboard.php"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Sai mật khẩu!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Tài khoản không tồn tại!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
