<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/db.php';

// Delete user
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: users.php");
    exit();
}

// Get all users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinSense - Manage Users</title>
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
        <h2 class="admin-title">👥 Manage Users</h2>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" 
                   placeholder="🔍 Search users..."
                   onkeyup="searchTable()">
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <table class="admin-table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="users.php?delete=<?php echo $user['id']; ?>"
                               class="btn-delete"
                               onclick="return confirm('Are you sure you want to delete this user?')">
                                🗑️ Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#aaa;">
                            No users registered yet
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
    </script>

</body>
</html>