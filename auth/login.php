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
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "არასწორი პაროლი.";
        }
    } else {
        $error = "ელფოსტა ვერ მოიძებნა.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = "ფორმის მონაცემები არ მიღებულა. გთხოვთ, შეავსოთ ყველა ველი.";
}
?>

<link rel="stylesheet" href="../css/auth.css?v=<?php echo time(); ?>">

<div class="auth-form">
    <h1>შესვლა</h1>
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="email">ელფოსტა</label>
        <input type="email" id="email" name="email" required>
        <label for="password">პაროლი</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">შესვლა</button>
        <p>არ გაქვს ანგარიში? <a href="register.php">რეგისტრაცია</a> | <a href="../index.php">მთავარ გვერდზე</a></p>
    </form>
</div>

<?php
$conn->close();
include '../includes/footer.php';
?>