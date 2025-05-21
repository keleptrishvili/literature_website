<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, email, role FROM users WHERE user_id = $user_id";
$user = $conn->query($user_query)->fetch_assoc();

$reviews_query = "SELECT r.rating, b.title
                 FROM reviews r
                 JOIN books b ON r.book_id = b.book_id
                 WHERE r.user_id = $user_id";
$reviews = $conn->query($reviews_query);
?>

<h1>Profile</h1>
<p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

<h2>Your Reviews</h2>
<table>
    <tr>
        <th>Book</th>
        <th>Rating</th>
    </tr>
    <?php while ($review = $reviews->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($review['title']); ?></td>
            <td><?php echo $review['rating']; ?>/5</td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>