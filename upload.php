<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Detect My Skin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body class="gradient-bg">
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
                    <li><a href="#" class="btn-logout" onclick="openLogoutModal(event)">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="upload-container">
        <div class="upload-box">
            <h2>Upload Your Photo 📸</h2>
            <p>Take a clear front-facing photo for best results</p>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="process_upload.php" method="POST" 
                  enctype="multipart/form-data">

                <!-- Image Preview -->
                <div class="preview-area" id="previewArea"
                     onclick="document.getElementById('imageInput').click()">
                    <img id="previewImg" src="#" alt="Preview" 
                         style="display:none; max-width:100%; 
                                max-height:300px; border-radius:10px;">
                    <div id="placeholder">
                        <span>🖼️</span>
                        <p>Click to upload your photo</p>
                    </div>
                </div>

                <input type="file" id="imageInput" name="image"
                       accept="image/*" style="display:none;" required>

                <button type="button" class="btn-upload"
                        onclick="document.getElementById('imageInput').click()">
                    Choose Photo
                </button>

                <button type="submit" class="btn-main" id="analyzeBtn"
                        style="display:none; margin-left:10px;">
                    Analyze My Skin →
                </button>

            </form>

            <!-- Tips -->
            <div class="tips">
                <h4>📋 Tips for Best Results</h4>
                <ul>
                    <li>✅ Use good lighting</li>
                    <li>✅ Face the camera directly</li>
                    <li>✅ Remove glasses if possible</li>
                    <li>✅ No heavy makeup</li>
                </ul>
            </div>

        </div>

        <div class="disclaimer-side">
            <div class="disclaimer-icon">⚠️</div>
            <strong>Not a medical diagnosis</strong>
            This tool gives an AI-based estimate for general skincare guidance only. It's not a substitute for professional dermatological advice. If you have persistent skin concerns, please consult a dermatologist.
        </div>
    </div>

    <script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('previewImg');
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                document.getElementById('placeholder').style.display = 'none';
                document.getElementById('analyzeBtn').style.display = 'inline-block';
            }
            reader.readAsDataURL(file);
        }
    });
    </script>

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