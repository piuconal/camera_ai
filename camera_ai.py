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

def is_duplicate_image(camera_id, object_name, timestamp):
    sql = "SELECT COUNT(*) FROM images WHERE camera_id = %s AND animal_name = %s AND created_at = %s"
    cursor.execute(sql, (camera_id, object_name, timestamp))
    return cursor.fetchone()[0] > 0

def save_image_to_db(camera_id, object_name, image_path, timestamp):
    if not is_duplicate_image(camera_id, object_name, timestamp):
        sql = "INSERT INTO images (camera_id, animal_name, information, created_at) VALUES (%s, %s, %s, %s)"
        cursor.execute(sql, (camera_id, object_name, image_path, timestamp))
        db.commit()
        print(f"✅ Đã lưu: {object_name} - {image_path} ({timestamp})")
    else:
        print(f"⚠ Ảnh {object_name} với thời gian {timestamp} đã tồn tại, bỏ qua.")

def generate_frames():
    cap = cv2.VideoCapture(0)
    camera_id = 1
    previous_frame = None

    while cap.isOpened():
        ret, frame = cap.read()
        if not ret:
            break

        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        gray = cv2.GaussianBlur(gray, (21, 21), 0)

        person_detected = False

        if previous_frame is not None:
            frame_diff = cv2.absdiff(previous_frame, gray)
            _, thresh = cv2.threshold(frame_diff, 30, 255, cv2.THRESH_BINARY)
            motion_level = cv2.countNonZero(thresh)

            if motion_level > 5000:
                results = model(frame)
                for r in results:
                    for box in r.boxes:
                        class_id = int(box.cls[0])
                        label = model.names[class_id]
                        conf = box.conf[0].item()
                        
                        if label == "person":
                            person_detected = True
                            color = (0, 0, 255)
                        else:
                            color = (0, 255, 0)

                        x1, y1, x2, y2 = map(int, box.xyxy[0])
                        cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
                        cv2.putText(frame, f"{label} ({conf:.2f})", (x1, y1 - 10), 
                                    cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

                        if label in animal_classes or label == "person":
                            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                            image_filename = f"{label}_{timestamp.replace(':', '-').replace(' ', '_')}.jpg"
                            image_path = os.path.join(IMAGE_DIR, image_filename)
                            cv2.imwrite(image_path, frame)
                            save_image_to_db(camera_id, label, image_path, timestamp)

        if person_detected:
            cv2.putText(frame, "⚠ WARNING: Human detected!", (50, 50),
                        cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 3)

        previous_frame = gray.copy()

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
