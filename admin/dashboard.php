<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle adding a book with image upload
if (isset($_POST['add_book'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author_id = (int)$_POST['author_id'];
    $genre_id = (int)$_POST['genre_id'];
    $published_year = $conn->real_escape_string($_POST['published_year']);
    $description = $conn->real_escape_string($_POST['description']);
    $file_url = $conn->real_escape_string($_POST['file_url']);

    // Handle image upload
    $books_img = 'default.jpg'; // Default image if upload fails
    if (isset($_FILES['books_img']) && $_FILES['books_img']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid() . '_' . $_FILES['books_img']['name'];
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['books_img']['tmp_name'], $target_file)) {
            $books_img = $file_name;
        }
    }

    $query = "INSERT INTO books (title, author_id, genre_id, published_year, description, file_url, books_img) 
              VALUES ('$title', $author_id, $genre_id, '$published_year', '$description', '$file_url', '$books_img')";
    $conn->query($query);
    header("Location: dashboard.php");
    exit;
}

// Handle adding an author
if (isset($_POST['add_author'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $birth_year = $conn->real_escape_string($_POST['birth_year']);
    $nationality = $conn->real_escape_string($_POST['nationality']);
    $query = "INSERT INTO authors (name, birth_year, nationality) 
              VALUES ('$name', '$birth_year', '$nationality')";
    $conn->query($query);
    header("Location: dashboard.php");
    exit;
}

// Handle adding a genre
if (isset($_POST['add_genre'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $query = "INSERT INTO genres (name) VALUES ('$name')";
    $conn->query($query);
    header("Location: dashboard.php");
    exit;
}

$books_query = "SELECT b.book_id, b.title, b.books_img, a.name AS author_name, g.name AS genre_name
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

<h2>Add Book</h2>
<form action="dashboard.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Book Title" required>
    <select name="author_id" required>
        <option value="">Select Author</option>
        <?php
        $authors_for_select = $conn->query("SELECT * FROM authors");
        while ($author = $authors_for_select->fetch_assoc()):
        ?>
            <option value="<?php echo $author['author_id']; ?>"><?php echo htmlspecialchars($author['name']); ?></option>
        <?php endwhile; ?>
    </select>
    <select name="genre_id" required>
        <option value="">Select Genre</option>
        <?php
        $genres_for_select = $conn->query("SELECT * FROM genres");
        while ($genre = $genres_for_select->fetch_assoc()):
        ?>
            <option value="<?php echo $genre['genres_id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></option>
        <?php endwhile; ?>
    </select>
    <input type="date" name="published_year" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="text" name="file_url" placeholder="File URL" required>
    <input type="file" name="books_img" accept="image/*">
    <button type="submit" name="add_book">Add Book</button>
</form>

<h2>Add Author</h2>
<form action="dashboard.php" method="POST">
    <input type="text" name="name" placeholder="Author Name" required>
    <input type="date" name="birth_year" required>
    <input type="text" name="nationality" placeholder="Nationality" required>
    <button type="submit" name="add_author">Add Author</button>
</form>

<h2>Add Genre</h2>
<form action="dashboard.php" method="POST">
    <input type="text" name="name" placeholder="Genre Name" required>
    <button type="submit" name="add_genre">Add Genre</button>
</form>

<h2>Books</h2>
<table>
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Author</th>
        <th>Genre</th>
    </tr>
    <?php while ($book = $books->fetch_assoc()): ?>
        <tr>
            <td>
                <?php if (!empty($book['books_img'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($book['books_img']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" style="max-width: 50px; height: auto;">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </td>
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