<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/header.php';
include '../includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';
if (!empty($_POST)) {
    echo "Form submitted<br>";
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $check_query = "SELECT * FROM users WHERE email = '$email'";
    if ($conn->query($check_query)->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if ($conn->query($query)) {
            echo "Registration successful, redirecting...<br>";
            header("Location: login.php");
            exit;
        } else {
            $error = "Error registering: " . $conn->error;
        }
    }
} else {
    echo "No form data received<br>";
}
?>

<link rel="stylesheet" href="../css/auth.css?v=<?php echo time(); ?>">

<div class="auth-form">
    <h1>Register</h1>
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login</a> | <a href="../index.php">Back to Home</a></p>
    </form>
</div>

<?php
$conn->close();
include '../includes/footer.php';
?>