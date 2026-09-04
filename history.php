
<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Delete analysis
if(isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $userId = $_SESSION['user_id'];
    
    // Get image path first
    $getImg = mysqli_query($conn, 
        "SELECT image_path FROM skin_analysis 
         WHERE id='$deleteId' AND user_id='$userId'");
    $img = mysqli_fetch_assoc($getImg);
    
    // Delete image file
    if($img && file_exists($img['image_path'])) {
        unlink($img['image_path']);
    }
    
    // Delete from database
    mysqli_query($conn, 
        "DELETE FROM skin_analysis 
         WHERE id='$deleteId' AND user_id='$userId'");
    
    header("Location: history.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get all analyses for this user
$result = mysqli_query($conn, 
    "SELECT * FROM skin_analysis 
     WHERE user_id='$userId' 
     ORDER BY detected_at DESC");

$totalAnalyses = mysqli_num_rows($result);

// Count each skin type
$skinCounts = mysqli_query($conn,
    "SELECT skin_type, COUNT(*) as total 
     FROM skin_analysis 
     WHERE user_id='$userId' 
     GROUP BY skin_type");

$skinEmojis = [
    "oily" => "💦",
    "dry" => "🌵",
    "normal" => "✨",
    
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - My History</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>
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

    <div class="history-container">

        <h2 class="history-title">📋 My Skin Analysis History</h2>

        <!-- Summary Stats -->
        <div class="history-stats">
            <div class="history-stat">
                <h3><?php echo $totalAnalyses; ?></h3>
                <p>Total Analyses</p>
            </div>
            <?php
            // Reset result pointer
            $skinCounts = mysqli_query($conn,
                "SELECT skin_type, COUNT(*) as total 
                 FROM skin_analysis 
                 WHERE user_id='$userId' 
                 GROUP BY skin_type");
            while($count = mysqli_fetch_assoc($skinCounts)): ?>
            <div class="history-stat">
                <h3>
                    <?php echo $skinEmojis[$count['skin_type']] ?? '🔍'; ?>
                    <?php echo $count['total']; ?>
                </h3>
                <p><?php echo ucfirst($count['skin_type']); ?> Skin</p>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- History Cards -->
        <?php if($totalAnalyses == 0): ?>
        <div class="empty-history">
            <span>🔍</span>
            <h3>No analyses yet!</h3>
            <p>Upload your first photo to get started</p>
            <a href="upload.php" class="btn-main">Start Detection →</a>
        </div>

        <?php else: ?>
        <div class="history-grid">
            <?php
            // Reset result
            $result = mysqli_query($conn,
                "SELECT * FROM skin_analysis 
                 WHERE user_id='$userId' 
                 ORDER BY detected_at DESC");
            while($analysis = mysqli_fetch_assoc($result)): 
                $skinType = $analysis['skin_type'];
                $emoji = $skinEmojis[$skinType] ?? '🔍';
            ?>
            <div class="history-card">

                <!-- Image -->
                    <div class="history-image">
                        <?php if($analysis['image_path'] && file_exists($analysis['image_path'])): ?>
                        <img src="<?php echo $analysis['image_path']; ?>" 
                            alt="Skin photo"
                            style="width:100px; height:100px; object-fit:cover; 
                                    border-radius:12px; border:3px solid #f0e0e5;">
                        <?php else: ?>
                        <div class="no-image">🖼️</div>
                        <?php endif; ?>
                    </div>

                <!-- Info -->
                <div class="history-info">
                    <div class="history-badge">
                        <?php echo $emoji; ?> 
                        <?php echo ucfirst($skinType); ?> Skin
                    </div>
                    <p class="history-date">
                        🗓️ <?php echo date('d M Y, h:i A', 
                            strtotime($analysis['detected_at'])); ?>
                    </p>
                    <div class="history-actions">
                        <a href="result.php?id=<?php echo $analysis['id']; ?>" 
                           class="btn-view">View Result</a>
                        <a href="history.php?delete=<?php echo $analysis['id']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Delete this analysis?')">
                           🗑️ Delete
                        </a>
                    </div>
                </div>

            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

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