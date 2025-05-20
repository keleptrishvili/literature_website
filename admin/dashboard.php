<?php
include 'db.php';
session_start();

if ($_SESSION['role'] != 'admin') {
  die("შესვლა აკრძალულია");
}

$result = $conn->query("SELECT * FROM users");

while($row = $result->fetch_assoc()) {
  echo $row['username'] . " | " . $row['email'] . " | " . $row['role'] . "<br>";
}
?>
