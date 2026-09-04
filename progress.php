<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get all analyses ordered by date
$result = mysqli_query($conn,
    "SELECT * FROM skin_analysis 
     WHERE user_id='$userId' 
     ORDER BY detected_at ASC");

$analyses = [];
while($row = mysqli_fetch_assoc($result)) {
    $analyses[] = $row;
}

$totalAnalyses = count($analyses);

// Get most recent skin type
$latest = !empty($analyses) ? end($analyses) : null;

// Get most frequent skin type
$freqResult = mysqli_query($conn,
    "SELECT skin_type, COUNT(*) as total 
     FROM skin_analysis 
     WHERE user_id='$userId' 
     GROUP BY skin_type 
     ORDER BY total DESC 
     LIMIT 1");
$mostFrequent = mysqli_fetch_assoc($freqResult);

$skinEmojis = [
    "oily" => "💦",
    "dry" => "🌵",
    "normal" => "✨",
    "combination" => "⚖️"
];

$skinColors = [
    "oily" => "#4ECDC4",
    "dry" => "#FF6B6B",
    "normal" => "#95E1D3",
    "combination" => "#F8B500"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Progress Tracking</title>
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

    <div class="progress-container">

        <h2 class="progress-title">📊 My Skin Progress</h2>

        <?php if($totalAnalyses == 0): ?>
        <!-- Empty State -->
        <div class="empty-history">
            <span>📊</span>
            <h3>No data yet!</h3>
            <p>Upload your first photo to start tracking your progress</p>
            <a href="upload.php" class="btn-main">Start Detection →</a>
        </div>

        <?php else: ?>

        <!-- Summary Cards -->
        <div class="progress-stats">
            <div class="progress-stat-card">
                <span>🔍</span>
                <h3><?php echo $totalAnalyses; ?></h3>
                <p>Total Scans</p>
            </div>
            <div class="progress-stat-card">
                <span><?php echo $skinEmojis[$latest['skin_type']] ?? '🔍'; ?></span>
                <h3><?php echo ucfirst($latest['skin_type']); ?></h3>
                <p>Latest Skin Type</p>
            </div>
            <div class="progress-stat-card">
                <span><?php echo $skinEmojis[$mostFrequent['skin_type']] ?? '🔍'; ?></span>
                <h3><?php echo ucfirst($mostFrequent['skin_type']); ?></h3>
                <p>Most Frequent</p>
            </div>
            <div class="progress-stat-card">
                <span>📅</span>
                <h3><?php echo date('d M', strtotime($analyses[0]['detected_at'])); ?></h3>
                <p>First Scan</p>
            </div>
        </div>

        <!-- Skin Type Distribution -->
        <div class="progress-section">
            <h3>🎯 Skin Type Distribution</h3>
            <?php
            $distResult = mysqli_query($conn,
                "SELECT skin_type, COUNT(*) as total 
                 FROM skin_analysis 
                 WHERE user_id='$userId' 
                 GROUP BY skin_type 
                 ORDER BY total DESC");
            while($dist = mysqli_fetch_assoc($distResult)):
                $percentage = round(($dist['total'] / $totalAnalyses) * 100);
                $color = $skinColors[$dist['skin_type']] ?? '#e07b8a';
            ?>
            <div class="progress-bar-row">
                <div class="progress-bar-label">
                    <span><?php echo $skinEmojis[$dist['skin_type']] ?? '🔍'; ?></span>
                    <span><?php echo ucfirst($dist['skin_type']); ?> Skin</span>
                    <span class="progress-count"><?php echo $dist['total']; ?> times</span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" 
                         style="width:<?php echo $percentage; ?>%; 
                                background:<?php echo $color; ?>;">
                        <?php echo $percentage; ?>%
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Timeline -->
        <div class="progress-section">
            <h3>📅 Scan Timeline</h3>
            <div class="timeline">
                <?php foreach(array_reverse($analyses) as $index => $analysis): 
                    $skinType = $analysis['skin_type'];
                    $emoji = $skinEmojis[$skinType] ?? '🔍';
                    $color = $skinColors[$skinType] ?? '#e07b8a';
                ?>
                <div class="timeline-item">
                    <div class="timeline-dot" 
                         style="background:<?php echo $color; ?>;">
                        <?php echo $emoji; ?>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-badge"
                             style="border-color:<?php echo $color; ?>;">
                            <?php echo ucfirst($skinType); ?> Skin
                        </div>
                        <p class="timeline-date">
                            <?php echo date('d M Y, h:i A', 
                                strtotime($analysis['detected_at'])); ?>
                        </p>
                        <a href="result.php?id=<?php echo $analysis['id']; ?>"
                           class="btn-view" style="font-size:12px; padding:5px 12px;">
                            View Result
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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