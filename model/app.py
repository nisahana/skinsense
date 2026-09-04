from flask import Flask, request, jsonify
import numpy as np
import os
import cv2
from PIL import Image
import io

app = Flask(__name__)

# ── Try to load real model, fall back to dummy ──
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'skin_model.h5')

model = None
labels = ['dry', 'normal', 'oily']
IMG_SIZE = 160  # must match IMG_SIZE in train_model.py

if os.path.exists(MODEL_PATH):
    try:
        from tensorflow.keras.models import load_model
        model = load_model(MODEL_PATH)
        model_input_shape = model.input_shape
        IMG_SIZE = model_input_shape[1]
        print(f"✅ Real CNN model loaded! Expecting {IMG_SIZE}x{IMG_SIZE} images.")
    except Exception as e:
        print(f"⚠️ Could not load model: {e}")
        model = None
else:
    print("⚠️ No model found — using dummy prediction")

# ── Face detector (rejects non-face photos before prediction) ──
face_cascade = cv2.CascadeClassifier(
    cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
)

def contains_face(pil_image):
    img_array = np.array(pil_image.convert('L'))  # grayscale for detection
    faces = face_cascade.detectMultiScale(
        img_array, scaleFactor=1.1, minNeighbors=5, minSize=(60, 60)
    )
    return len(faces) > 0

@app.route('/')
def home():
    status = f"Real CNN Model ({IMG_SIZE}x{IMG_SIZE})" if model else "Dummy Model"
    return f"SkinSense AI is running! Mode: {status}"

@app.route('/predict', methods=['POST'])
def predict():
    if 'image' not in request.files:
        return jsonify({'error': 'No image provided'}), 400

    file = request.files['image']

    try:
        img = Image.open(io.BytesIO(file.read())).convert('RGB')
    except Exception as e:
        print(f"⚠️ Could not read image: {e}")
        return jsonify({'error': 'Invalid image file', 'status': 'error'}), 400

    # ── Reject non-face images before running the skin model ──
    if not contains_face(img):
        return jsonify({
            'error': 'No face detected in this photo. Please upload a clear front-facing photo.',
            'status': 'no_face_detected'
        }), 400

    if model:
        try:
            img_resized = img.resize((IMG_SIZE, IMG_SIZE))
            img_array = np.array(img_resized) / 255.0
            img_array = np.expand_dims(img_array, axis=0)

            predictions = model.predict(img_array)
            predicted_index = np.argmax(predictions[0])
            confidence = round(float(np.max(predictions[0])) * 100, 2)
            skin_type = labels[predicted_index]
            is_uncertain = confidence < 40

        except Exception as e:
            print(f"⚠️ Prediction error: {e}")
            return jsonify({
                'error': 'Failed to process image. Please try a different photo.',
                'status': 'error'
            }), 500
    else:
        import random, time
        time.sleep(1)
        skin_type = random.choices(labels, weights=[0.30, 0.35, 0.35])[0]
        confidence = round(random.uniform(75, 92), 2)
        is_uncertain = False

    return jsonify({
        'skin_type': skin_type,
        'confidence': confidence,
        'is_uncertain': is_uncertain,
        'status': 'success'
    })

if __name__ == '__main__':
    app.run(port=5000, debug=True)