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
    <link rel="stylesheet" href="<?php echo ($current_page === 'login.php' || $current_page === 'register.php' || $current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../css/style.css' : 'css/style.css'; ?>?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <nav>
            <a href="<?php echo ($current_page === 'login.php' || $current_page === 'register.php' || $current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../index.php' : 'index.php'; ?>">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo ($current_page === 'login.php' || $current_page === 'register.php' || $current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../profile.php' : 'profile.php'; ?>">Profile</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo ($current_page === 'login.php' || $current_page === 'register.php' || $current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? 'dashboard.php' : 'admin/dashboard.php'; ?>">Admin Dashboard</a>
                <?php endif; ?>
                <a href="<?php echo ($current_page === 'login.php' || $current_page === 'register.php' || $current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../logout.php' : 'logout.php'; ?>">Logout</a>
            <?php else: ?>
                <?php if ($current_page !== 'login.php' && $current_page !== 'register.php'): ?>
                    <a href="<?php echo ($current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../auth/login.php' : 'auth/login.php'; ?>">Login</a>
                    <a href="<?php echo ($current_page === 'dashboard.php' || $current_page === 'view.php' || $current_page === 'add_review.php') ? '../auth/register.php' : 'auth/register.php'; ?>">Register</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </header>
    <main>