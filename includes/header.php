<?php
session_start();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Literature Site</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a> 
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <?php if ($current_page !== 'login.php' && $current_page !== 'register.php'): ?>
                    <a href="auth/login.php">Login</a>
                    <a href="auth/register.php">Register</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </header>
    <main>