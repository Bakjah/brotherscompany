<?php include '../includes/config.php'; requireLogin(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Vehicle</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);">
  <div class="navbar-container">
    <div class="navbar-logo">🔧 Workshop</div>
    <a href="index.php" class="navbar-btn">Back</a>
  </div>
</nav>

<section class="section">
  <div class="container" style="max-width: 600px;">
    <h1>Post a Vehicle</h1>

    <div class="card">
      <form method="POST">
        <div class="form-group">
          <label>Vehicle Title</label>
          <input type="text" name="title" placeholder="e.g. Toyota Camry 2020" required>
        </div>

        <div class="form-group">
          <label>Category</label>
          <select name="category" required>
            <option>Cars</option>
            <option>Motorcycles</option>
          </select>
        </div>

        <div class="form-group">
          <label>Price ($)</label>
          <input type="number" name="price" step="0.01" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" required></textarea>
        </div>

        <div class="form-group">
          <label>Contact Phone</label>
          <input type="tel" name="contact_phone" required>
        </div>

        <button type="submit" class="btn btn-yellow">Post Vehicle</button>
      </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $title = sanitize($_POST['title']);
      $category = sanitize($_POST['category']);
      $price = (float)$_POST['price'];
      $description = sanitize($_POST['description']);
      $contact = sanitize($_POST['contact_phone']);
      
      mysqli_query($conn, "INSERT INTO marketplace_posts (user_id, title, category, price, description, contact_phone) VALUES ({$_SESSION['user_id']}, '$title', '$category', $price, '$description', '$contact')");
      echo '<p style="color: green;">Vehicle posted successfully!</p>';
    }
    ?>
  </div>
</section>

</body>
</html>
