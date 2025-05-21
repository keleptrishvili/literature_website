<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$books_query = "SELECT b.book_id, b.title, a.name AS author_name, g.name AS genre_name
                FROM books b
                JOIN authors a ON b.author_id = a.author_id
                JOIN genres g ON b.genre_id = g.genres_id";
$books = $conn->query($books_query);

$authors_query = "SELECT * FROM authors";
$authors = $conn->query($authors_query);

$genres_query = "SELECT * FROM genres";
$genres = $conn->query($genres_query);

$reviews_query = "SELECT r.review_id, u.username, b.title, r.rating
                 FROM reviews r
                 JOIN users u ON r.user_id = u.user_id
                 JOIN books b ON r.book_id = b.book_id";
$reviews = $conn->query($reviews_query);

$users_query = "SELECT * FROM users";
$users = $conn->query($users_query);
?>

<h1>Admin Dashboard</h1>

<h2>Books</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
    </tr>
    <?php while ($book = $books->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($book['title']); ?></td>
            <td><?php echo htmlspecialchars($book['author_name']); ?></td>
            <td><?php echo htmlspecialchars($book['genre_name']); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Authors</h2>
<table>
    <tr>
        <th>Name</th>
        <th>Birth Year</th>
        <th>Nationality</th>
    </tr>
    <?php while ($author = $authors->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($author['name']); ?></td>
            <td><?php echo htmlspecialchars($author['birth_year']); ?></td>
            <td><?php echo htmlspecialchars($author['nationality']); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Genres</h2>
<table>
    <tr>
        <th>Name</th>
    </tr>
    <?php while ($genre = $genres->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($genre['name']); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Reviews</h2>
<table>
    <tr>
        <th>Username</th>
        <th>Book</th>
        <th>Rating</th>
    </tr>
    <?php while ($review = $reviews->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($review['username']); ?></td>
            <td><?php echo htmlspecialchars($review['title']); ?></td>
            <td><?php echo $review['rating']; ?>/5</td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Users</h2>
<table>
    <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
    </tr>
    <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$conn->close();
include '../includes/footer.php';
?>