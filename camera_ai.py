import cv2
import torch
import os
import mysql.connector
from ultralytics import YOLO
from flask import Flask, Response
from datetime import datetime

app = Flask(__name__)

# Kết nối MySQL
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="camera_ai"
)
cursor = db.cursor()

# Load YOLOv8 model
model = YOLO("yolov8s.pt")

# Get class names
coco_classes = model.names
animal_classes = [name for name in coco_classes.values() if name not in ["person", "bicycle", "car", "motorcycle", "airplane", "bus", "train", "truck", "boat", "bottle"]]

# Thư mục lưu ảnh
IMAGE_DIR = "static/images/"
os.makedirs(IMAGE_DIR, exist_ok=True)

def is_duplicate_image(camera_id, animal_name, timestamp):
    """Kiểm tra xem ảnh có bị trùng (cùng tên + cùng thời gian) không."""
    sql = "SELECT COUNT(*) FROM images WHERE camera_id = %s AND animal_name = %s AND created_at = %s"
    cursor.execute(sql, (camera_id, animal_name, timestamp))
    count = cursor.fetchone()[0]
    return count > 0

def save_image_to_db(camera_id, animal_name, image_path, timestamp):
    """Lưu thông tin ảnh vào bảng images nếu không bị trùng."""
    if not is_duplicate_image(camera_id, animal_name, timestamp):
        sql = "INSERT INTO images (camera_id, animal_name, information, created_at) VALUES (%s, %s, %s, %s)"
        cursor.execute(sql, (camera_id, animal_name, image_path, timestamp))
        db.commit()
        print(f"✅ Đã lưu: {animal_name} - {image_path} ({timestamp})")
    else:
        print(f"⚠ Ảnh {animal_name} với thời gian {timestamp} đã tồn tại, bỏ qua.")

def generate_frames():
    cap = cv2.VideoCapture(0)  # Chỉnh camera index nếu cần
    camera_id = 1  # ID giả định, có thể lấy từ request hoặc config

    while cap.isOpened():
        ret, frame = cap.read()
        if not ret:
            break

        results = model(frame)
        person_detected = False

        for r in results:
            for box in r.boxes:
                class_id = int(box.cls[0])
                label = model.names[class_id]
                conf = box.conf[0].item()

                if label in animal_classes or label == "person":
                    x1, y1, x2, y2 = map(int, box.xyxy[0])
                    color = (0, 255, 0) if label != "person" else (0, 0, 255)
                    cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
                    cv2.putText(frame, f"{label} ({conf:.2f})", (x1, y1 - 10), 
                                cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

                    if label == "person":
                        person_detected = True
                    elif label in animal_classes:
                        # Lưu ảnh khi phát hiện động vật
                        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")  # Lấy thời gian chính xác
                        image_filename = f"{label}_{timestamp.replace(':', '-').replace(' ', '_')}.jpg"
                        image_path = os.path.join(IMAGE_DIR, image_filename)
                        cv2.imwrite(image_path, frame)

                        # Lưu vào database (tránh trùng lặp)
                        save_image_to_db(camera_id, label, image_path, timestamp)

        if person_detected:
            cv2.putText(frame, "⚠ WARNING: Human detected!", (50, 50),
                        cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 3)

        _, buffer = cv2.imencode('.jpg', frame)
        frame_bytes = buffer.tobytes()
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

    cap.release()

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
