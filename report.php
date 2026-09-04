<?php
session_start();
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get all analyses
$result = mysqli_query($conn,
    "SELECT * FROM skin_analysis 
     WHERE user_id='$userId' 
     ORDER BY detected_at DESC");

$analyses = [];
while($row = mysqli_fetch_assoc($result)) {
    $analyses[] = $row;
}

$totalAnalyses = count($analyses);

// Get skin type counts
$distResult = mysqli_query($conn,
    "SELECT skin_type, COUNT(*) as total 
     FROM skin_analysis 
     WHERE user_id='$userId' 
     GROUP BY skin_type 
     ORDER BY total DESC");

$skinDist = [];
while($row = mysqli_fetch_assoc($distResult)) {
    $skinDist[] = $row;
}

// Most frequent skin type
$mostFrequent = !empty($skinDist) ? $skinDist[0] : null;
$latest = !empty($analyses) ? $analyses[0] : null;

$skinEmojis = [
    "oily" => "💦",
    "dry" => "🌵",
    "normal" => "✨",
    "combination" => "⚖️"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - My Report</title>
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

    <div class="report-container">

        <div class="report-header-box">
            <div>
                <h2 class="report-title">📄 My Skin Report</h2>
                <p style="color:#777;">Generated on <?php echo date('d M Y'); ?></p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="generate_pdf.php" class="btn-print">
                    📥 Download PDF
                </a>
                <button onclick="window.print()" class="btn-print" 
                        style="background:white; color:#e07b8a; 
                            border:2px solid #e07b8a;">
                    🖨️ Print
                </button>
            </div>
        </div>

        <?php if($totalAnalyses == 0): ?>
        <div class="empty-history">
            <span>📄</span>
            <h3>No data to generate report!</h3>
            <p>Upload your first photo to get started</p>
            <a href="upload.php" class="btn-main">Start Detection →</a>
        </div>

        <?php else: ?>

        <!-- Report Card -->
        <div class="report-card" id="reportContent">

            <!-- Report Header -->
            <div class="report-top">
                <div class="report-logo">🧴 SkinSense</div>
                <div class="report-info">
                    <h3>Skin Analysis Report</h3>
                    <p>Name: <strong><?php echo $userName; ?></strong></p>
                    <p>Date: <strong><?php echo date('d M Y'); ?></strong></p>
                    <p>Total Analyses: <strong><?php echo $totalAnalyses; ?></strong></p>
                </div>
            </div>

            <hr class="report-divider">

            <!-- Summary -->
            <div class="report-summary">
                <h3>📊 Summary</h3>
                <div class="report-summary-grid">
                    <div class="report-summary-item">
                        <span><?php echo $skinEmojis[$latest['skin_type']] ?? '🔍'; ?></span>
                        <p>Latest Skin Type</p>
                        <h4><?php echo ucfirst($latest['skin_type']); ?></h4>
                    </div>
                    <div class="report-summary-item">
                        <span><?php echo $skinEmojis[$mostFrequent['skin_type']] ?? '🔍'; ?></span>
                        <p>Most Frequent</p>
                        <h4><?php echo ucfirst($mostFrequent['skin_type']); ?></h4>
                    </div>
                    <div class="report-summary-item">
                        <span>🔍</span>
                        <p>Total Scans</p>
                        <h4><?php echo $totalAnalyses; ?></h4>
                    </div>
                </div>
            </div>

            <hr class="report-divider">

            <!-- Skin Type Distribution -->
            <div class="report-section">
                <h3>🎯 Skin Type Distribution</h3>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Skin Type</th>
                            <th>Times Detected</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($skinDist as $dist): 
                            $percentage = round(($dist['total'] / $totalAnalyses) * 100);
                        ?>
                        <tr>
                            <td>
                                <?php echo $skinEmojis[$dist['skin_type']] ?? '🔍'; ?>
                                <?php echo ucfirst($dist['skin_type']); ?> Skin
                            </td>
                            <td><?php echo $dist['total']; ?> times</td>
                            <td>
                                <div class="mini-bar">
                                    <div class="mini-bar-fill" 
                                         style="width:<?php echo $percentage; ?>%">
                                    </div>
                                    <span><?php echo $percentage; ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <hr class="report-divider">

            <!-- Analysis History Table -->
            <div class="report-section">
                <h3>📋 Analysis History</h3>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Skin Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($analyses as $index => $analysis): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo date('d M Y, h:i A', 
                                strtotime($analysis['detected_at'])); ?></td>
                            <td>
                                <?php echo $skinEmojis[$analysis['skin_type']] ?? '🔍'; ?>
                                <?php echo ucfirst($analysis['skin_type']); ?> Skin
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <hr class="report-divider">

            <!-- Recommendations -->
            <div class="report-section">
                <h3>✨ Recommended Skincare Routine</h3>
                <p style="color:#777; margin-bottom:15px;">
                    Based on your most frequent skin type: 
                    <strong><?php echo ucfirst($mostFrequent['skin_type']); ?> Skin</strong>
                </p>
                <?php
                $recResult = mysqli_query($conn,
                    "SELECT * FROM recommendations 
                     WHERE skin_type='{$mostFrequent['skin_type']}'");
                $step = 1;
                while($rec = mysqli_fetch_assoc($recResult)): ?>
                <div class="report-rec-row">
                    <div class="report-rec-step">Step <?php echo $step++; ?></div>
                    <div>
                        <strong><?php echo $rec['step']; ?></strong>
                        <p><?php echo $rec['tip']; ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Footer -->
            <div class="report-footer">
                <p>Generated by SkinSense AI System • <?php echo date('d M Y'); ?></p>
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