<?php
session_start();

function restrictToAdmin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php?error=გთხოვთ, შეხვიდეთ სისტემაში");
        exit;
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../index.php?error=წვდომა შეზღუდულია: თქვენ არ ხართ ადმინი");
        exit;
    }
}

include '../includes/header.php';
include '../includes/db.php';

restrictToAdmin();

$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$book = null;
if ($book_id > 0) {
    $query = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$book) {
    header("Location: dashboard.php?error=წიგნი არ მოიძებნა");
    exit;
}

$error = '';
if (isset($_POST['edit_book'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author_id = (int)$_POST['author_id'];
    $genre_id = (int)$_POST['genre_id'];
    $published_year = $conn->real_escape_string($_POST['published_year']);
    $description = $conn->real_escape_string($_POST['description']);
    $file_url = $conn->real_escape_string($_POST['file_url']);
    $books_img = $book['books_img'];

    if (isset($_FILES['books_img']) && $_FILES['books_img']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../Uploads/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024;

        $file_type = $_FILES['books_img']['type'];
        $file_size = $_FILES['books_img']['size'];
        $file_tmp = $_FILES['books_img']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['books_img']['name']);

        if (!in_array($file_type, $allowed_types)) {
            $error = "შეცდომა: მხოლოდ JPEG, PNG და GIF ფაილებია დაშვებული.";
        } elseif ($file_size > $max_size) {
            $error = "შეცდომა: ფაილის ზომა აღემატება 5MB-ს.";
        } else {
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $target_file)) {
                if ($book['books_img'] != 'default.jpg' && file_exists($upload_dir . $book['books_img'])) {
                    unlink($upload_dir . $book['books_img']);
                }
                $books_img = $file_name;
            } else {
                $error = "შეცდომა: ფოტოს ატვირთვა ვერ მოხერხდა.";
            }
        }
    }

    if (!isset($error)) {
        $query = "UPDATE books SET title = ?, author_id = ?, genre_id = ?, published_year = ?, description = ?, file_url = ?, books_img = ? WHERE book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siissssi", $title, $author_id, $genre_id, $published_year, $description, $file_url, $books_img, $book_id);
        if ($stmt->execute()) {
            header("Location: dashboard.php?success=წიგნი წარმატებით განახლდა");
        } else {
            $error = "შეცდომა: წიგნის განახლება ვერ მოხერხდა: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<h1>წიგნის რედაქტირება</h1>
<?php if (isset($error)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>
<form action="edit_book.php?book_id=<?php echo $book_id; ?>" method="POST" enctype="multipart/form-data">
    <label for="title">სათაური</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
    <label for="author_id">ავტორი</label>
    <select id="author_id" name="author_id" required>
        <?php
        $authors = $conn->query("SELECT * FROM authors");
        while ($author = $authors->fetch_assoc()):
        ?>
            <option value="<?php echo $author['author_id']; ?>" <?php if ($author['author_id'] == $book['author_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($author['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <label for="genre_id">ჟანრი</label>
    <select id="genre_id" name="genre_id" required>
        <?php
        $genres = $conn->query("SELECT * FROM genres");
        while ($genre = $genres->fetch_assoc()):
        ?>
            <option value="<?php echo $genre['genres_id']; ?>" <?php if ($genre['genres_id'] == $book['genre_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($genre['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <label for="published_year">გამოცემის წელი</label>
    <input type="date" id="published_year" name="published_year" value="<?php echo htmlspecialchars($book['published_year']); ?>" required>
    <label for="description">აღწერა</label>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($book['description']); ?></textarea>
    <label for="file_url">ფაილის URL</label>
    <input type="text" id="file_url" name="file_url" value="<?php echo htmlspecialchars($book['file_url']); ?>" required>
    <label for="books_img">ფოტო (ატვირთე ახალი, თუ გინდა)</label>
    <input type="file" id="books_img" name="books_img" accept="image/jpeg,image/png,image/gif">
    <?php if (!empty($book['books_img']) && $book['books_img'] != 'default.jpg'): ?>
        <p>მიმდინარე ფოტო: <img src="../Uploads/<?php echo htmlspecialchars($book['books_img']); ?>" alt="current photo" style="max-width: 100px; height: auto;"></p>
    <?php endif; ?>
    <button type="submit" name="edit_book">განახლება</button>
</form>

<?php
$conn->close();
include '../includes/footer.php';
?>