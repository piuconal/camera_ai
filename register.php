<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $sql = "INSERT INTO users (username, password, fullname, phone, email, address, role, status) 
            VALUES ('$username', '$password', '$fullname', '$phone', '$email', '$address', 0, 0)";

    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success">Đăng ký thành công!</div>';
    } else {
        echo '<div class="alert alert-danger">Lỗi: ' . $conn->error . '</div>';
    }

    $conn->close();
}
?>
