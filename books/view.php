<?php
include '../includes/header.php';
include '../includes/db.php';


$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$query = "SELECT b.title, b.description, b.file_url, a.name AS author_name, g.name AS genre_name
          FROM books b
          JOIN authors a ON b.author_id = a.author_id
          JOIN genres g ON b.genre_id = g.genres_id
          WHERE b.book_id = $book_id";
$book_result = $conn->query($query);
$book = $book_result->fetch_assoc();

$review_query = "SELECT r.rating, u.username
                 FROM reviews r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.book_id = $book_id";
$reviews = $conn->query($review_query);
?>

<h1><?php echo htmlspecialchars($book['title']); ?></h1>
<p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
<p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre_name']); ?></p>
<p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
<p><a href="<?php echo htmlspecialchars($book['file_url']); ?>" download>Download Book</a></p>

<h2>Reviews</h2>
<table>
    <tr>
        <th>Username</th>
        <th>Rating</th>
    </tr>
    <?php while ($review = $reviews->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($review['username']); ?></td>
            <td><?php echo $review['rating']; ?>/5</td>
        </tr>
    <?php endwhile; ?>
</table>

<?php if (isset($_SESSION['user_id'])): ?>
    <h3>Add a Review</h3>
    <form action="reviews/add_review.php" method="POST">
        <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
        <label>Rating (1-5): <input type="number" name="rating" min="1" max="5" required></label>
        <button type="submit">Submit Review</button>
    </form>
<?php endif; ?>

<?php
$conn->close();
include __DIR__ . '/../includes/footer.php';

?>