<?php
require_once 'includes/db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,500;0,600;1,500;1,600&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>

    <nav>
        <div class="nav-container">
            <h1 class="logo">🧴 SkinSense</h1>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-box">

            <div class="auth-header">
                <span>🧴</span>
                <h2>Welcome Back!</h2>
                <p>Login to continue your skincare journey</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email"
                           placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password"
                               placeholder="Enter your password" required>
                        <span class="eye-icon" 
                              onclick="togglePassword('password')">👁️</span>
                    </div>
                </div>

                <div style="text-align:right; margin-top:-10px; margin-bottom:15px;">
                    <a href="forgot_password.php"
                       style="color:#e07b8a; font-size:13px; text-decoration:none;">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="btn-full">Login →</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account?
                    <a href="register.php">Register here</a>
                </p>
            </div>

        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if(field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
        </script>

</body>
</html>