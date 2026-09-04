<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1 class="logo">🧴 SkinSense</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <?php
    $step = isset($_GET['step']) ? $_GET['step'] : '1';
    ?>

    <div class="auth-container">
        <div class="auth-box">

            <?php if($step == '1'): ?>
            <!-- Step 1: Enter Email -->
            <div class="auth-header">
                <span>🔑</span>
                <h2>Forgot Password?</h2>
                <p>No worries! Enter your email and we'll help you reset it.</p>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="forgot_password.php?step=2" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email"
                           placeholder="Enter your registered email" required>
                </div>

                <button type="submit" class="btn-full">Send Reset Code →</button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? 
                    <a href="login.php">Login here</a>
                </p>
            </div>

            <?php elseif($step == '2'): ?>
            <!-- Step 2: Enter Reset Code -->
            <div class="auth-header">
                <span>📧</span>
                <h2>Check Your Email</h2>
                <p>We sent a 6-digit reset code to your email address.</p>
            </div>

            <!-- Progress Steps -->
            <div class="reset-steps">
                <div class="reset-step done">1</div>
                <div class="reset-line done"></div>
                <div class="reset-step active">2</div>
                <div class="reset-line"></div>
                <div class="reset-step">3</div>
            </div>

            <form action="forgot_password.php?step=3" method="POST">
                <div class="form-group">
                    <label>Enter 6-Digit Code</label>
                    <input type="text" name="code"
                           placeholder="e.g. 123456"
                           maxlength="6" required
                           style="text-align:center; font-size:22px; letter-spacing:8px;">
                </div>

                <button type="submit" class="btn-full">Verify Code →</button>
            </form>

            <div class="auth-footer">
                <p>Didn't receive the code? 
                    <a href="forgot_password.php">Resend</a>
                </p>
            </div>

            <?php elseif($step == '3'): ?>
            <!-- Step 3: Enter New Password -->
            <div class="auth-header">
                <span>🔒</span>
                <h2>Reset Password</h2>
                <p>Enter your new password below.</p>
            </div>

            <!-- Progress Steps -->
            <div class="reset-steps">
                <div class="reset-step done">1</div>
                <div class="reset-line done"></div>
                <div class="reset-step done">2</div>
                <div class="reset-line done"></div>
                <div class="reset-step active">3</div>
            </div>

            <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password"
                           placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password"
                           placeholder="Repeat new password" required>
                </div>

                <button type="submit" class="btn-full">Reset Password →</button>
            </form>

            <?php elseif($step == 'success'): ?>
            <!-- Success -->
            <div class="auth-header">
                <span>✅</span>
                <h2>Password Reset!</h2>
                <p>Your password has been successfully reset.</p>
            </div>

            <div class="success-box">
                <p>You can now login with your new password.</p>
            </div>

            <a href="login.php" class="btn-full" 
               style="display:block; text-align:center; text-decoration:none; margin-top:20px;">
                Go to Login →
            </a>

            <?php endif; ?>

        </div>
    </div>

</body>
</html>