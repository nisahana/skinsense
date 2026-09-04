<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/db.php';

// Get real stats from database
$totalUsers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$totalAnalyses = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM skin_analysis"))[0];
$totalRoutines = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT skin_type) FROM recommendations"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style1.css">
</head>
<body>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1 class="logo">🧴 SkinSense Admin</h1>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="routines.php">Manage Routines</a></li>
                <li><a href="../index.php">Back to Site</a></li>
                <li><a href="#" class="btn-logout" onclick="openLogoutModal(event)">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-container">

        <h2 class="admin-title">📊 Admin Dashboard</h2>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">👥</span>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🔍</span>
                <div class="stat-info">
                    <h3>Total Analyses</h3>
                    <p class="stat-number"><?php echo $totalAnalyses; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🧴</span>
                <div class="stat-info">
                    <h3>Skin Types</h3>
                    <p class="stat-number"><?php echo $totalRoutines; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📊</span>
                <div class="stat-info">
                    <h3>Reports Generated</h3>
                    <p class="stat-number">--</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="users.php" class="btn-main">👥 Manage Users</a>
                <a href="routines.php" class="btn-main">🧴 Manage Routines</a>
            </div>
        </div>

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