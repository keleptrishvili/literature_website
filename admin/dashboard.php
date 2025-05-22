<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}


if (isset($_POST['add_book'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author_id = (int)$_POST['author_id'];
    $genre_id = (int)$_POST['genre_id'];
    $published_year = $conn->real_escape_string($_POST['published_year']);
    $description = $conn->real_escape_string($_POST['description']);
    $file_url = $conn->real_escape_string($_POST['file_url']);

    $books_img = 'default.jpg'; 
    if (isset($_FILES['books_img']) && $_FILES['books_img']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../Uploads/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; 

        $file_type = $_FILES['books_img']['type'];
        $file_size = $_FILES['books_img']['size'];
        $file_tmp = $_FILES['books_img']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['books_img']['name']);

        if (!in_array($file_type, $allowed_types)) {
            $error = "Error: Only JPEG, PNG, and GIF files are allowed.";
        } elseif ($file_size > $max_size) {
            $error = "Error: File size exceeds 5MB.";
        } else {

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $books_img = $file_name;
            } else {
                $error = "Error: Failed to upload image.";
            }
        }
    } elseif ($_FILES['books_img']['error'] != UPLOAD_ERR_NO_FILE) {
        $error = "Error: File upload error (Code: " . $_FILES['books_img']['error'] . ").";
    }

    if (!isset($error)) {
        $query = "INSERT INTO books (title, author_id, genre_id, published_year, description, file_url, books_img) 
                  VALUES ('$title', $author_id, $genre_id, '$published_year', '$description', '$file_url', '$books_img')";
        if ($conn->query($query)) {
            header("Location: dashboard.php?success=Book added successfully");
        } else {
            $error = "Error: Failed to add book to database: " . $conn->error;
        }
    }
}
?>

<h1>Admin Dashboard</h1>
<?php if (isset($error)): ?>
    <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div style="color: green; text-align: center; margin-bottom: 1rem;"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

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
    <input type="file" name="books_img" accept="image/jpeg,image/png,image/gif">
    <button type="submit" name="add_book">Add Book</button>
</form>