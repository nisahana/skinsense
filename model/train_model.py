import tensorflow as tf
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.models import Model
from tensorflow.keras.layers import GlobalAveragePooling2D, Dense, Dropout, BatchNormalization, Input
from tensorflow.keras.regularizers import l2
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint, ReduceLROnPlateau
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.losses import CategoricalCrossentropy
from sklearn.utils.class_weight import compute_class_weight
from sklearn.metrics import classification_report, confusion_matrix
import os
import numpy as np

# ── Dataset path ──
DATASET_PATH = r"C:\Users\DELL\OneDrive - POLITEKNIK UNGKU OMAR\Documents\SUB UNIKL\SEM 7\FYP 2\archive\Oily-Dry-Skin-Types"

TRAIN_PATH = os.path.join(DATASET_PATH, 'train')
VALID_PATH = os.path.join(DATASET_PATH, 'valid')
TEST_PATH  = os.path.join(DATASET_PATH, 'test')

IMG_SIZE    = 160
BATCH_SIZE  = 16
NUM_CLASSES = 3

print("Using MobileNetV2 — two-phase training")

# ── Data Augmentation ──
train_datagen = ImageDataGenerator(
    rescale=1./255,
    rotation_range=15,
    width_shift_range=0.1,
    height_shift_range=0.1,
    horizontal_flip=True,
    zoom_range=0.1,
    brightness_range=[0.8, 1.2]
)

valid_datagen = ImageDataGenerator(rescale=1./255)
test_datagen  = ImageDataGenerator(rescale=1./255)

# ── Load Data ──
print("\nLoading data...")
train_data = train_datagen.flow_from_directory(
    TRAIN_PATH,
    target_size=(IMG_SIZE, IMG_SIZE),
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    shuffle=True
)

valid_data = valid_datagen.flow_from_directory(
    VALID_PATH,
    target_size=(IMG_SIZE, IMG_SIZE),
    batch_size=BATCH_SIZE,
    class_mode='categorical'
)

test_data = test_datagen.flow_from_directory(
    TEST_PATH,
    target_size=(IMG_SIZE, IMG_SIZE),
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    shuffle=False  # keep order so confusion matrix labels line up
)

print("Classes found:", train_data.class_indices)
print("Train samples:", train_data.samples)
print("Valid samples:", valid_data.samples)
print("Test samples:", test_data.samples)

# ── Class weights (fixes class imbalance automatically) ──
class_labels = list(train_data.class_indices.values())
class_weights_array = compute_class_weight(
    class_weight='balanced',
    classes=np.array(class_labels),
    y=train_data.classes
)
class_weights = dict(zip(class_labels, class_weights_array))
print("\nClass weights (to correct imbalance):", class_weights)
print("Per-class image counts:", {
    k: int(np.sum(train_data.classes == v)) for k, v in train_data.class_indices.items()
})

# ── Build Model ──
print("\nBuilding MobileNetV2 model...")

base_model = MobileNetV2(
    weights='imagenet',
    include_top=False,
    input_shape=(IMG_SIZE, IMG_SIZE, 3)
)

# Phase 1: freeze the ENTIRE base model so only the new head trains first
base_model.trainable = False

inputs = Input(shape=(IMG_SIZE, IMG_SIZE, 3))
x = base_model(inputs, training=False)
x = GlobalAveragePooling2D()(x)
x = Dense(128, activation='relu', kernel_regularizer=l2(0.01))(x)
x = BatchNormalization()(x)
x = Dropout(0.5)(x)
outputs = Dense(NUM_CLASSES, activation='softmax', kernel_regularizer=l2(0.01))(x)

model = Model(inputs, outputs)

model.compile(
    optimizer=Adam(learning_rate=0.001),
    loss=CategoricalCrossentropy(label_smoothing=0.1),
    metrics=['accuracy']
)

print(f"Trainable layers (Phase 1 — head only): {len([l for l in model.layers if l.trainable])}")

callbacks_phase1 = [
    EarlyStopping(monitor='val_accuracy', patience=5, restore_best_weights=True, verbose=1),
    ReduceLROnPlateau(monitor='val_loss', factor=0.5, patience=3, min_lr=1e-7, verbose=1)
]

# ── Phase 1: train head only ──
print("\n=== PHASE 1: Training classifier head (base frozen) ===")
model.fit(
    train_data,
    epochs=15,
    validation_data=valid_data,
    callbacks=callbacks_phase1,
    class_weight=class_weights,
    verbose=1
)

# ── Phase 2: unfreeze last 20 layers, fine-tune with a lower LR ──
print("\n=== PHASE 2: Fine-tuning (last 20 layers unfrozen) ===")
base_model.trainable = True
for layer in base_model.layers[:-20]:
    layer.trainable = False

model.compile(
    optimizer=Adam(learning_rate=0.00003),  # slightly higher than before, still conservative
    loss=CategoricalCrossentropy(label_smoothing=0.1),
    metrics=['accuracy']
)

print(f"Trainable layers (Phase 2 — fine-tuning): {len([l for l in model.layers if l.trainable])}")

callbacks_phase2 = [
    EarlyStopping(monitor='val_accuracy', patience=8, restore_best_weights=True, verbose=1),
    ModelCheckpoint('model/skin_model.h5', monitor='val_accuracy', save_best_only=True, verbose=1),
    ReduceLROnPlateau(monitor='val_loss', factor=0.5, patience=3, min_lr=1e-7, verbose=1)
]

history = model.fit(
    train_data,
    epochs=30,
    validation_data=valid_data,
    callbacks=callbacks_phase2,
    class_weight=class_weights,
    verbose=1
)

# ── Evaluate ──
print("\n=== Evaluating on test data ===")
test_loss, test_accuracy = model.evaluate(test_data)
print(f"Test Accuracy: {test_accuracy * 100:.2f}%")
print(f"Test Loss: {test_loss:.4f}")

# ── Confusion matrix — tells us WHICH skin type is failing ──
print("\n=== Confusion Matrix (rows=actual, cols=predicted) ===")
class_names = list(train_data.class_indices.keys())
predictions = model.predict(test_data)
y_pred = np.argmax(predictions, axis=1)
y_true = test_data.classes

print(confusion_matrix(y_true, y_pred))
print("\n=== Classification Report ===")
print(classification_report(y_true, y_pred, target_names=class_names))

# ── Save ──
model.save('model/skin_model.h5')
print("\nModel saved!")
print("Training complete!")