<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = (int)$_POST['book_id'];
$rating = (int)$_POST['rating'];

$query = "INSERT INTO reviews (user_id, book_id, rating) VALUES ($user_id, $book_id, $rating)";
if ($conn->query($query)) {
    header("Location: ../books/view.php?book_id=$book_id");
} else {
    echo "Error adding review: " . $conn->error;
}

$conn->close();
?>