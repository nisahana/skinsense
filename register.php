<?php
require_once 'includes/db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        
        if(mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into database
            $sql = "INSERT INTO users (name, email, password) 
                    VALUES ('$name', '$email', '$hashed_password')";
            
            if(mysqli_query($conn, $sql)) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Register</title>
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
                <span>✨</span>
                <h2>Create Account</h2>
                <p>Join SkinSense and start your skincare journey!</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if(isset($success)): ?>
            <div class="alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name"
                           placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email"
                           placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password"
                            placeholder="Create a password" required>
                        <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password"
                            placeholder="Repeat your password" required>
                        <span class="eye-icon" onclick="togglePassword('confirm_password')">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn-full">Create Account →</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account?
                    <a href="login.php">Login here</a>
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