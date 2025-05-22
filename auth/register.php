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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $check_query = "SELECT * FROM users WHERE email = '$email'";
    if ($conn->query($check_query)->num_rows > 0) {
        $error = "ელფოსტა უკვე არსებობს.";
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if ($conn->query($query)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "რეგისტრაციისას მოხდა შეცდომა: " . $conn->error;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = "ფორმის მონაცემები არ მიღებულა. გთხოვთ, შეავსოთ ყველა ველი.";
}
?>

<link rel="stylesheet" href="../css/auth.css?v=<?php echo time(); ?>">

<div class="auth-form">
    <h1>რეგისტრაცია</h1>
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <label for="username">მომხმარებლის სახელი</label>
        <input type="text" id="username" name="username" required>
        <label for="email">ელფოსტა</label>
        <input type="email" id="email" name="email" required>
        <label for="password">პაროლი</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">რეგისტრაცია</button>
        <p>უკვე გაქვს ანგარიში? <a href="login.php">შესვლა</a> | <a href="../index.php">მთავარ გვერდზე</a></p>
    </form>
</div>

<?php
$conn->close();
include '../includes/footer.php';
?>