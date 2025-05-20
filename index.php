<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container">
  <h1>📚 ყველა წიგნი</h1>

  <?php
  $sql = "SELECT * FROM books";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo "<table><tr><th>სათაური</th><th>ავტორი</th><th>წელი</th></tr>";
    while ($row = $result->fetch_assoc()) {
      echo "<tr><td>" . $row["title"] . "</td><td>" . $row["author"] . "</td><td>" . $row["year"] . "</td></tr>";
    }
    echo "</table>";
  } else {
    echo "წიგნები ვერ მოიძებნა.";
  }
  ?>
</div>

<?php include 'includes/footer.php'; ?>
