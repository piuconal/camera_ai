import cv2

for i in range(10):  # Kiểm tra 10 index đầu
    cap = cv2.VideoCapture(i, cv2.CAP_DSHOW)  # Sử dụng DirectShow để nhận diện
    if cap.isOpened():
        print(f"Camera index {i} hoạt động")
        cap.release()
    else:
        print(f"Camera index {i} không hoạt động")
