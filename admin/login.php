<?php

session_start();

$admin_username = "admin";
$admin_password = "admin123";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = "Admin";
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav>
        <div class="nav-container">
            <h1 class="logo">🧴 SkinSense</h1>
            <ul>
                <li><a href="../index.php">Back to Site</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-box">

            <div class="auth-header">
                <span>🔐</span>
                <h2>Admin Access</h2>
                <p>Login to manage the SkinSense system</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           placeholder="Enter admin username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                           placeholder="Enter admin password" required>
                </div>

                <button type="submit" class="btn-full">Login as Admin →</button>
            </form>

            <div class="auth-footer">
                <p><a href="../index.php">← Back to main site</a></p>
            </div>

        </div>
    </div>

</body>
</html>