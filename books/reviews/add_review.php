<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

// Validate input
if ($book_id <= 0 || $rating < 1 || $rating > 5) {
    header("Location: ../view.php?book_id=$book_id&error=Invalid input");
    exit;
}

// Check if the user has already reviewed this book
$check_query = "SELECT * FROM reviews WHERE user_id = $user_id AND book_id = $book_id";
$check_result = $conn->query($check_query);
if ($check_result->num_rows > 0) {
    header("Location: ../view.php?book_id=$book_id&error=You have already reviewed this book");
    exit;
}

// Insert the review
$query = "INSERT INTO reviews (user_id, book_id, rating) VALUES ($user_id, $book_id, $rating)";
if ($conn->query($query)) {
    header("Location: ../view.php?book_id=$book_id&success=Review submitted successfully");
} else {
    header("Location: ../view.php?book_id=$book_id&error=Error adding review: " . urlencode($conn->error));
}

$conn->close();
?>