<?php
include '../includes/header.php';
include '../includes/db.php';

// გამართვისთვის შეცდომების ჩვენება
error_reporting(E_ALL);
ini_set('display_errors', 1);

// მიიღე book_id GET პარამეტრიდან
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

// SQL მოთხოვნა prepared statement-ით
$query = "SELECT b.title, b.description, b.file_url, b.books_img, a.name AS author_name, g.name AS genre_name
          FROM books b
          JOIN authors a ON b.author_id = a.author_id
          JOIN genres g ON b.genre_id = g.genres_id
          WHERE b.book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book_result = $stmt->get_result();

if ($book_result && $book_result->num_rows > 0) {
    $book = $book_result->fetch_assoc();
} else {
    die("წიგნი ვერ მოიძებნა. შეამოწმეთ book_id: " . $book_id);
}
$stmt->close();

// საშუალო რეიტინგის გამოთვლა
$avg_rating_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE book_id = ?";
$stmt = $conn->prepare($avg_rating_query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$avg_rating_result = $stmt->get_result();
$avg_rating = $avg_rating_result ? $avg_rating_result->fetch_assoc()['avg_rating'] : null;
$stmt->close();

// მიმოხილვების მოთხოვნა
$review_query = "SELECT r.rating, u.username
                 FROM reviews r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.book_id = ?";
$stmt = $conn->prepare($review_query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();

// შეამოწმე, აქვს თუ არა მომხმარებელს ამ წიგნზე მიმოხილვა
$user_review = null;
if (isset($_SESSION['user_id'])) {
    $user_review_query = "SELECT rating FROM reviews WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($user_review_query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
    $stmt->execute();
    $user_review_result = $stmt->get_result();
    if ($user_review_result && $user_review_result->num_rows > 0) {
        $user_review = $user_review_result->fetch_assoc();
    }
    $stmt->close();
}

// შეტყობინებების მიღება
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<h1><?php echo htmlspecialchars($book['title']); ?></h1>
<p><strong>ავტორი:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
<p><strong>ჟანრი:</strong> <?php echo htmlspecialchars($book['genre_name']); ?></p>

<?php
// სურათის გზის გამართვა
$image_path = !empty($book['books_img']) ? "../uploads/" . htmlspecialchars($book['books_img']) : '';
$image_exists = $image_path && file_exists($image_path);
?>

<?php if ($image_exists): ?>
    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($book['title']); ?> cover" style="max-width: 200px; height: auto; margin-bottom: 1rem;" onerror="this.src='../uploads/default.jpg'; console.log('სურათის ჩატვირთვა ვერ მოხერხდა: <?php echo $image_path; ?>');">
<?php else: ?>
    <p>ამ წიგნისთვის სურათი არ არის ხელმისაწვდომი.</p>
    <?php if (!empty($book['books_img'])): ?>
        <p style="color: red;">შეცდომა: სურათის ფაილი (<?php echo $image_path; ?>) ვერ მოიძებნა.</p>
    <?php endif; ?>
<?php endif; ?>

<p><strong>აღწერა:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
<p><strong>საშუალო რეიტინგი:</strong> <?php echo $avg_rating ? number_format($avg_rating, 1) . '/5' : 'ჯერ არ არის მიმოხილვები'; ?></p>
<p><a href="<?php echo htmlspecialchars($book['file_url']); ?>" download>წიგნის ჩამოტვირთვა</a></p>

<?php if ($success): ?>
    <div style="color: green; text-align: center; margin-bottom: 1rem;"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></div>
<?php endif; ?>

<h2>მიმოხილვები</h2>
<table>
    <tr>
        <th>მომხმარებლის სახელი</th>
        <th>რეიტინგი</th>
    </tr>
    <?php if ($reviews && $reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($review['username']); ?></td>
                <td><?php echo $review['rating']; ?>/5</td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">ჯერ არ არის მიმოხილვები.</td>
        </tr>
    <?php endif; ?>
</table>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($user_review): ?>
        <h3>თქვენი მიმოხილვა</h3>
        <p>თქვენ შეაფასეთ ეს წიგნი: <strong><?php echo $user_review['rating']; ?>/5</strong></p>
    <?php else: ?>
        <h3>მიმოხილვის დამატება</h3>
        <form action="reviews/add_review.php" method="POST">
            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
            <label>რეიტინგი (1-5): <input type="number" name="rating" min="1" max="5" required></label>
            <button type="submit">მიმოხილვის გაგზავნა</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php
$conn->close();
include __DIR__ . '/../includes/footer.php';
?>