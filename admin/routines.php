<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/db.php';

// Handle update routine tip
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $tip = $_POST['tip'];
    mysqli_query($conn, "UPDATE recommendations SET tip='$tip' WHERE id='$id'");
    $success = "Routine updated successfully!";
}

// Get all recommendations grouped by skin type
$result = mysqli_query($conn, "SELECT * FROM recommendations ORDER BY skin_type, id");
$routines = [];
while($row = mysqli_fetch_assoc($result)) {
    $routines[$row['skin_type']][] = $row;
}

$skinEmojis = [
    'oily' => '💦',
    'dry' => '🌵',
    'normal' => '✨',
    
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Manage Routines</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style1.css">
</head>
<body>

    <nav>
        <div class="nav-container">
            <h1 class="logo">🧴 SkinSense Admin</h1>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="routines.php">Manage Routines</a></li>
                <li><a href="../index.php">Back to Site</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-container">
        <h2 class="admin-title">🧴 Manage Skincare Routines</h2>

        <?php if(isset($success)): ?>
        <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="routine-grid">
            <?php foreach($routines as $skinType => $steps): ?>
            <div class="routine-admin-card">
                <div class="routine-admin-header">
                    <span><?php echo $skinEmojis[$skinType] ?? '🧴'; ?></span>
                    <h3><?php echo ucfirst($skinType); ?> Skin</h3>
                </div>

                <?php foreach($steps as $step): ?>
                <div class="routine-edit-row">
                    <strong><?php echo $step['step']; ?></strong>
                    <form action="routines.php" method="POST" 
                          style="display:flex; gap:10px; margin-top:8px;">
                        <input type="hidden" name="id" 
                               value="<?php echo $step['id']; ?>">
                        <input type="text" name="tip" 
                               value="<?php echo $step['tip']; ?>"
                               class="routine-input">
                        <button type="submit" name="update" 
                                class="btn-save">Save</button>
                    </form>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endforeach; ?>
        </div>

    </div>

</body>
</html>