<?php
include 'db.php';

if (isset($_POST['register'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
  $conn->query($sql);
  echo "რეგისტრაცია წარმატებით დასრულდა!";
}
?>

<form method="post">
  <input name="username" placeholder="Username"><br>
  <input name="email" placeholder="Email"><br>
  <input name="password" type="password" placeholder="Password"><br>
  <button name="register">რეგისტრაცია</button>
</form>
