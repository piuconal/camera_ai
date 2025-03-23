<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FontAwesome để hiển thị icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        #animalList {
            max-height: 140px; /* Giới hạn chiều cao tối đa */
            overflow-y: auto; /* Thêm thanh cuộn dọc khi danh sách quá dài */
            border: 1px solid #ddd; /* Tạo viền nhẹ để dễ nhìn hơn */
            border-radius: 5px;
        }
        #loginMessage {
            position: absolute;
            top: 15%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20%;
            z-index: 10000;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #toggle-mode {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 50%;
        }

        #toggle-mode:hover {
            background: rgba(0, 0, 0, 0.9);
        }
        
        /* Nội dung chính */
        .content {
            position: relative;
            margin-top: 20px;
            width: 80%;
            height: 20vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 20px;
            background: rgba(55, 54, 54, 0.6);
        }

        /* Modal Background */
        .modal-content {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }

        /* Nút mở chatbot */
        .chatbot-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Modal chatbot */
        .chatbot-modal .modal-dialog {
            position: fixed;
            bottom: 20px;
            right: 20px;
            margin: 0;
            width: 350px; /* Điều chỉnh kích thước phù hợp */
            max-width: 100%;
        }

        .chat-box {
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
        }
        .message {
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .modal-backdrop {
            display: none !important;
        }

        /* Đảm bảo modal hiển thị đúng vị trí */
        .chatbot-modal {
            background: transparent !important;
        }


        /* Định vị modal ở góc trái dưới */
        .weather-modal .modal-dialog {
            position: fixed;
            bottom: 20px;
            left: 20px;
            margin: 0;
            width: 270px;
            max-width: 100%;
        }

        /* Thiết kế modal */
        .weather-modal .modal-content {
            background: rgba(148, 205, 252, 0.75);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Nút mở modal thời tiết */
        .weather-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #ffcc00;
            color: #333;
            border: none;
            padding: 10px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .weather-button:hover {
            background: #ffdb4d;
        }

        .search-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30%;
            text-align: center;
        }

        #animalInfo {
            text-align: center;
            padding: 15px;
        }
        #animalInfo .modal-body {
            max-height: 500px; /* Giới hạn chiều cao */
            overflow-y: auto; /* Cuộn khi nội dung dài */
        }
        .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        z-index: 1000;
        border-radius: 20px;
    }
    .popup-content {
        position: relative;
        text-align: center;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }
    </style>
</head>
<body>
<div id="loginMessage"></div>

<!-- Video Background -->
<div class="video-background">
    <video id="background-video" autoplay muted loop>
        <source id="video-source" src="video/background_morning.mp4" type="video/mp4">
        Trình duyệt của bạn không hỗ trợ video.
    </video>
</div>

<!-- Button Toggle Dark/Light Mode -->
<button id="toggle-mode">
    <i class="fas fa-sun"></i> <!-- Icon mặc định -->
</button>

<!-- Nội dung chính -->
<div class="container-fluid content">
    <div>
        <h2>Nghiên cứu và phát triển phần mềm quản lý và giám sát rừng thông minh</h2>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</button>
        <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
    </div>
</div>

<!-- Hiển thị thông báo -->
<div class="container mt-3">
    <div id="alertBox"></div>
</div>

<!-- Nút mở chatbot -->
<button class="chatbot-button" data-bs-toggle="modal" data-bs-target="#chatbotModal">💬</button>

<!-- Modal Chatbot -->
<div class="modal fade chatbot-modal" id="chatbotModal" tabindex="-1" aria-labelledby="chatbotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatbotModalLabel">Có thể bạn không biết?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chat-box p-2 border mb-2" id="chatBox">
                    <!-- Tin nhắn sẽ hiển thị tại đây -->
                </div>
                <form id="chatForm">
                    <div class="input-group">
                        <input type="text" id="userMessage" class="form-control" placeholder="Nhập tin nhắn..." required>
                        <button class="btn btn-primary" type="submit">Gửi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Nút mở modal thời tiết -->
<button class="weather-button" data-bs-toggle="modal" data-bs-target="#weatherModal">🌤️</button>

<!-- Modal thời tiết -->
<div class="modal fade weather-modal" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="weatherInfo">
                    <p>Đang tải dữ liệu thời tiết...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng Ký -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Đăng Ký</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">Tên tài khoản</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ và tên đầy đủ</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng Nhập -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Đăng Nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Tài khoản</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Đăng Nhập</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="search-container">
    <button class="btn btn-primary mt-2 mb-2" onclick="showPopup()">Tra thông tin</button>
    <input type="text" id="searchAnimal" class="form-control" placeholder="Nhập tên động vật muốn biết thêm thông tin..." oninput="searchAnimal()">
    <ul id="animalList" class="list-group mt-2"></ul>
</div>
<div id="animalInfo"></div>
<!-- Popup -->
<div id="popup" class="popup d-none">
    <div class="popup-content text-center">
        <span class="close" onclick="closePopup()">&times;</span>
        <h4>Tải ảnh lên</h4>
        <input type="file" id="imageUpload" class="form-control-file" accept="image/*" onchange="uploadAndProcessImage()">
        <div class="mt-3">
            <img id="preview" src="#" alt="Ảnh xem trước" class="img-fluid d-none" style="max-width: 100%; height: 200px; object-fit: contain;">
        </div>
        <h4 class="mt-3">Thông tin:</h4>
        <div id="loadingSpinner" class="d-none mt-3">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div id="info"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#registerForm").submit(function(event) {
            event.preventDefault();
            $.post("register.php", $(this).serialize(), function(response) {
                $("#alertBox").html(response);
                $("#registerModal").modal('hide');
            });
        });

        document.getElementById("loginForm").addEventListener("submit", async function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch("login.php", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }

                const result = await response.json();

                if (result && result.status === "success" && result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    const loginMessage = document.getElementById("loginMessage");
                    if (loginMessage) {
                        loginMessage.innerHTML = `<div class="alert alert-danger">${result.message || "Login failed"}</div>`;
                    }
                }
            } catch (error) {
                console.error("Error during login:", error);
            }
        });

    });
</script>
<script>
    document.getElementById("toggle-mode").addEventListener("click", function() {
        let video = document.getElementById("background-video");
        let source = document.getElementById("video-source");
        let button = document.getElementById("toggle-mode");
        let icon = button.querySelector("i");

        if (source.getAttribute("src") === "video/background_morning.mp4") {
            source.setAttribute("src", "video/background_night.mp4");
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
        } else {
            source.setAttribute("src", "video/background_morning.mp4");
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
        }

        video.load(); // Load lại video mới
    });
</script>
<script src="js/chatbot.js"></script>
<script src="js/weather.js"></script>
<script src="js/search_animal.js"></script>
<script>
    function showPopup() {
        document.getElementById('popup').classList.remove('d-none');
    }

    function closePopup() {
        document.getElementById('popup').classList.add('d-none');
    }

    function uploadAndProcessImage() {
        const input = document.getElementById('imageUpload');
        const preview = document.getElementById('preview');
        const spinner = document.getElementById('loadingSpinner');
        const formData = new FormData();
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            formData.append("image", file);
            
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
            
            spinner.classList.remove('d-none');
            
            fetch("http://localhost:5001/process_image", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("info").innerHTML = `<p><strong>${data.name}</strong>: ${data.confidence}%</p><p>${data.info}</p>`;
            })
            .catch(error => console.error("Error:", error))
            .finally(() => {
                spinner.classList.add('d-none');
            });
        }
    }

</script>

</body>
</html>
