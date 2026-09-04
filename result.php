<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get analysis from database
$analysisId = isset($_GET['id']) ? $_GET['id'] : 0;
$userId = $_SESSION['user_id'];

$result = mysqli_query($conn, 
    "SELECT * FROM skin_analysis WHERE id='$analysisId' AND user_id='$userId'");
$analysis = mysqli_fetch_assoc($result);

if(!$analysis) {
    header("Location: upload.php");
    exit();
}

$skinType = $analysis['skin_type'];

// Get recommendations from database
$recResult = mysqli_query($conn, 
    "SELECT * FROM recommendations WHERE skin_type='$skinType'");

// Skin descriptions
$descriptions = [
    "oily" => "Your skin produces excess sebum, making it look shiny especially in the T-zone area.",
    "dry" => "Your skin lacks moisture and may feel tight, rough or flaky in some areas.",
    "normal" => "Your skin is well-balanced not too oily or too dry. Lucky you!",
];

$emojis = [
    "oily" => "💦",
    "dry" => "🌵", 
    "normal" => "✨",
];

$description = $descriptions[$skinType] ?? "Unable to determine skin type. Please try again with a clearer photo.";
$emoji = $emojis[$skinType] ?? "🔍";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Your Results</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>

    <nav>
        <div class="nav-container">
            <a href="home.php" style="text-decoration:none;">
                <h1 class="logo">🧴 SkinSense</h1>
            </a>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="upload.php">Detect My Skin</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="report.php">My Report</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li style="color:#e07b8a; font-weight:bold;">
                        👋 <?php echo $_SESSION['user_name']; ?>
                    </li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="results-container">

        <!-- Uploaded Image -->
        <div class="result-card">
            <img src="<?php echo $analysis['image_path']; ?>" 
                 alt="Your skin photo"
                 style="width:200px; height:200px; 
                        object-fit:cover; border-radius:50%;
                        border: 4px solid #e07b8a; margin-bottom:20px;">

            <div class="result-header">
                <span class="result-emoji"><?php echo $emoji; ?></span>
                <h2>Your Skin Type</h2>
                <div class="skin-type-badge">
                    <?php echo ucfirst($skinType); ?> Skin
                </div>
                <p class="skin-description"><?php echo $description; ?></p>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="recommendations">
            <h3>✨ Your Personalized Skincare Routine</h3>
            <div class="routine-cards">
                <?php 
                $step = 1;
                while($rec = mysqli_fetch_assoc($recResult)): ?>
                <div class="routine-card">
                    <div class="step-number">Step <?php echo $step++; ?></div>
                    <h4><?php echo $rec['step']; ?></h4>
                    <p><?php echo $rec['tip']; ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="result-actions">
            <a href="upload.php" class="btn-outline">Try Again</a>
            <a href="history.php" class="btn-main">View History</a>
        </div>

    </div>

</body>
</html>