<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    
    $image = $_FILES['image'];
    $userId = $_SESSION['user_id'];
    
    // Validate image
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    
    if(!in_array($image['type'], $allowedTypes)) {
        header("Location: upload.php?error=Please upload JPG or PNG image only!");
        exit();
    }
    
    // Create uploads folder if not exists
    if(!file_exists('uploads/')) {
        mkdir('uploads/', 0777, true);
    }
    
    // Generate unique filename
    $filename = time() . '_' . $userId . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $uploadPath = 'uploads/' . $filename;
    
    if(move_uploaded_file($image['tmp_name'], $uploadPath)) {
        
        // Call the Flask AI API to get the real skin type prediction.
        // Uses AI_SERVICE_URL when hosted (e.g. Railway); falls back to
        // localhost for local development with Laragon.
        $flaskUrl = getenv('AI_SERVICE_URL') ?: 'http://127.0.0.1:5000/predict';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $flaskUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'image' => new CURLFile(realpath($uploadPath))
        ]);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            // Flask server not reachable — fallback so the app doesn't crash
            $skinType = "unknown";
        } else {
            $result = json_decode($response, true);
            $skinType = $result['skin_type'] ?? "unknown";
        }
        
        curl_close($curl);
        
        // Save to database
        $sql = "INSERT INTO skin_analysis (user_id, image_path, skin_type) 
                VALUES ('$userId', '$uploadPath', '$skinType')";
        mysqli_query($conn, $sql);
        
        // Get the analysis id
        $analysisId = mysqli_insert_id($conn);
        
        // Redirect to result page
        header("Location: result.php?id=$analysisId");
        exit();
        
    } else {
        header("Location: upload.php?error=Failed to upload image. Please try again.");
        exit();
    }
    
} else {
    header("Location: upload.php");
    exit();
}
?>