<?php
include 'includes/header.php';
include 'includes/db.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT b.book_id, b.title, b.books_img, a.name AS author_name, g.name AS genre_name, AVG(r.rating) AS avg_rating
          FROM books b
          JOIN authors a ON b.author_id = a.author_id
          JOIN genres g ON b.genre_id = g.genres_id
          LEFT JOIN reviews r ON b.book_id = r.book_id
          WHERE b.title LIKE '%$search%'
          GROUP BY b.book_id";
$result = $conn->query($query);
?>

<h1>Welcome to the Literature Site</h1>

<form action="index.php" method="GET">
    <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<h2>Books</h2>
<table>
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
        <th>Average Rating</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if (!empty($row['books_img'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['books_img']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?> cover" style="max-width: 50px; height: auto;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                <td><?php echo htmlspecialchars($row['genre_name']); ?></td>
                <td><?php echo $row['avg_rating'] ? number_format($row['avg_rating'], 1) : 'No reviews'; ?></td>
                <td><a href="books/view.php?book_id=<?php echo $row['book_id']; ?>">View</a></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No books found.</td>
        </tr>
    <?php endif; ?>
</table>

<?php
$conn->close();
include 'includes/footer.php';
?>