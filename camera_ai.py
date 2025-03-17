import cv2
import torch
from ultralytics import YOLO

# Load mô hình YOLOv8 mới nhất (phiên bản nhỏ gọn nhưng chính xác)
model = YOLO("yolov8s.pt")  # Có thể dùng "yolov8l.pt" hoặc "yolov8x.pt" cho độ chính xác cao hơn

# Lấy danh sách tất cả các lớp từ mô hình COCO
coco_classes = model.names

# Lọc các lớp động vật (các lớp COCO trừ phương tiện, đồ vật, v.v.)
animal_classes = [name for name in coco_classes.values() if name not in ["person", "bicycle", "car", "motorcycle", "airplane", "bus", "train", "truck", "boat", "bottle"]]

# Mở camera
cap = cv2.VideoCapture(0)  # 0 là webcam, có thể thay bằng đường dẫn video

while cap.isOpened():
    ret, frame = cap.read()
    if not ret:
        break

    # Chạy mô hình nhận diện
    results = model(frame)

    person_detected = False  # Kiểm tra xem có người không

    # Duyệt qua kết quả nhận diện
    for r in results:
        boxes = r.boxes  # Lấy danh sách khung nhận diện
        for box in boxes:
            class_id = int(box.cls[0])
            label = model.names[class_id]
            conf = box.conf[0].item()  # Độ chính xác

            if label in animal_classes or label == "person":
                x1, y1, x2, y2 = map(int, box.xyxy[0])  # Lấy tọa độ khung
                color = (0, 255, 0) if label != "person" else (0, 0, 255)  # Xanh lá cho động vật, đỏ cho người
                
                cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)

                text = f"{label} ({conf:.2f})"
                cv2.putText(frame, text, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

                if label == "person":
                    person_detected = True  # Đánh dấu có người

    # Cảnh báo nếu phát hiện con người
    if person_detected:
        cv2.putText(frame, "⚠ WARNING: Human detected!", (50, 50),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 3)

    # Hiển thị hình ảnh
    cv2.imshow("Animal & Human Detection", frame)

    # Nhấn 'q' để thoát
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
