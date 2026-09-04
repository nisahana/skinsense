<?php
session_start();
require_once 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - AI Skin Type Detection</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="home.php" style="text-decoration:none;">
                <h1 class="logo">🧴 SkinSense</h1>
            </a>
            <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="upload.php">Detect My Skin</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="progress.php">Progress</a></li>
            <li><a href="report.php">My Report</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="#" class="btn-logout" onclick="openLogoutModal(event)">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
            </ul>
        </div>
    </nav>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-content">
        <div class="mascot">🧴</div>
        <span class="sticker-badge">✨ AI-Powered Skin Analysis</span>
        <h2>Know Your Skin Type</h2>
        <p>Upload your facial photo and let our AI detect your skin type instantly!</p>
        <a href="upload.php" class="btn-main">Start Detection →</a>
    </div>
</div>
<svg class="wavy-divider" viewBox="0 0 1440 60" preserveAspectRatio="none">
    <path fill="#FBF6EF" d="M0,32L60,26.7C120,21,240,11,360,16C480,21,600,43,720,48C840,53,960,43,1080,34.7C1200,27,1320,21,1380,18.7L1440,16L1440,60L0,60Z"></path>
</svg>

    <!-- Features Section -->
        <div class="features">
            <a href="upload.php" class="feature-card" style="text-decoration:none;">
                <span>🤖</span>
                <h3>AI Detection</h3>
                <p>Advanced CNN model analyzes your skin type accurately</p>
            </a>
            <a href="upload.php" class="feature-card" style="text-decoration:none;">
                <span>💡</span>
                <h3>Smart Recommendations</h3>
                <p>Get personalized skincare routines based on your skin type</p>
            </a>
            <a href="progress.php" class="feature-card" style="text-decoration:none;">
                <span>📊</span>
                <h3>Progress Tracking</h3>
                <p>Monitor your skin condition changes over time</p>
            </a>
        </div>

<!-- Admin Access -->
<div style="text-align:center; padding:20px;">
    <a href="admin/login.php" 
       style="color:#ccc; font-size:12px; text-decoration:none;">
        Admin Access
    </a>
</div>

<!-- Logout Confirmation Modal -->
<div class="logout-modal-overlay" id="logoutModal">
    <div class="logout-modal">
        <div class="modal-icon">🧴</div>
        <h3>Leaving already?</h3>
        <p>Are you sure you want to log out of SkinSense?</p>
        <div class="logout-modal-buttons">
            <button type="button" class="btn-modal-cancel" onclick="closeLogoutModal()">Cancel</button>
            <button type="button" class="btn-modal-confirm" onclick="window.location.href='logout.php'">Yes, Logout</button>
        </div>
    </div>
</div>

<script>
function openLogoutModal(e) {
    e.preventDefault();
    document.getElementById('logoutModal').classList.add('active');
}
function closeLogoutModal() {
    document.getElementById('logoutModal').classList.remove('active');
}
</script>

</body>
</html>