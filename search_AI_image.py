from flask import Flask, request, jsonify
import os
import tensorflow as tf
import numpy as np
import cv2
import requests
from deep_translator import GoogleTranslator
from tensorflow.keras.applications.mobilenet_v2 import MobileNetV2, preprocess_input
from tensorflow.keras.preprocessing import image
from tensorflow.keras.applications.mobilenet_v2 import decode_predictions
from flask_cors import CORS 

app = Flask(__name__)
CORS(app)

# Load mô hình AI
model = MobileNetV2(weights='imagenet')

def predict_animal(img_path):
    img = image.load_img(img_path, target_size=(224, 224))
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = preprocess_input(img_array)

    predictions = model.predict(img_array)
    decoded_predictions = decode_predictions(predictions, top=3)[0]

    result = [{"name": name, "confidence": float(score) * 100} for (_, name, score) in decoded_predictions]
    return result

def get_animal_info(animal_name):
    url = f"https://en.wikipedia.org/api/rest_v1/page/summary/{animal_name}"
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        return data.get("extract", "Không tìm thấy thông tin.")
    return "Không tìm thấy thông tin."

def translate_text(text):
    return GoogleTranslator(source="auto", target="vi").translate(text)

@app.route('/process_image', methods=['POST'])
def process_image():
    if 'image' not in request.files:
        return jsonify({"error": "No image uploaded"}), 400

    img_file = request.files['image']
    img_path = "uploaded_image.jpg"
    img_file.save(img_path)

    results = predict_animal(img_path)
    
    if results:
        top_result = results[0]
        animal_name = top_result["name"]
        confidence = top_result["confidence"]
        info = get_animal_info(animal_name)
        translated_info = translate_text(info)

        return jsonify({
            "name": animal_name,
            "confidence": confidence,
            "info": translated_info,
        })
    
    return jsonify({"error": "No prediction made"}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)
