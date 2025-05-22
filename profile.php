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

$reviews_query = "SELECT r.rating, b.title, g.name AS genre_name, b.book_id
                 FROM reviews r
                 JOIN books b ON r.book_id = b.book_id
                 JOIN genres g ON b.genre_id = g.genres_id
                 WHERE r.user_id = $user_id
                 ORDER BY r.review_id DESC";
$reviews = $conn->query($reviews_query);

// Get statistics
$stats_query = "SELECT COUNT(*) as total_reviews, AVG(rating) as avg_given_rating FROM reviews WHERE user_id = $user_id";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<h1>Profile</h1>
<p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

<h2>Review Statistics</h2>
<p><strong>Total Reviews:</strong> <?php echo $stats['total_reviews']; ?></p>
<p><strong>Average Rating Given:</strong> <?php echo $stats['avg_given_rating'] ? number_format($stats['avg_given_rating'], 1) . '/5' : 'No reviews yet'; ?></p>

<h2>Your Reviews</h2>
<table>
    <tr>
        <th>Book</th>
        <th>Genre</th>
        <th>Your Rating</th>
        <th>Action</th>
    </tr>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($review['title']); ?></td>
                <td><?php echo htmlspecialchars($review['genre_name']); ?></td>
                <td><?php echo $review['rating']; ?>/5</td>
                <td><a href="books/view.php?book_id=<?php echo $review['book_id']; ?>">View Book</a></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">You have not submitted any reviews yet.</td>
        </tr>
    <?php endif; ?>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>