<?php
include '../includes/header.php';
include '../includes/db.php';

$query = "SELECT b.book_id, b.title, a.name AS author_name
          FROM books b
          JOIN authors a ON b.author_id = a.author_id";
$result = $conn->query($query);
?>

<h1>Book List</h1>
<ul>
    <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <a href="view.php?book_id=<?php echo $row['book_id']; ?>">
                <?php echo htmlspecialchars($row['title']); ?> by <?php echo htmlspecialchars($row['author_name']); ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>

<?php
$conn->close();
include '../includes/footer.php';
?>