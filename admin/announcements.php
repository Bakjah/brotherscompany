<?php include '../includes/config.php'; requireAdmin(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Announcements</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
  <div class="navbar-container">
    <div class="navbar-logo">📢 Announcements</div>
    <a href="index.php" class="navbar-btn" style="background: white; color: #1e40af;">Back</a>
  </div>
</nav>

<section class="section">
  <div class="container" style="max-width: 600px;">
    <?php if (!isset($_GET['action']) || $_GET['action'] !== 'create'): ?>
      <h1>📢 Manage Announcements</h1>
      <a href="?action=create" class="btn btn-primary" style="margin-bottom: 2rem;">+ Create New</a>

      <?php
      if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        mysqli_query($conn, "DELETE FROM announcements WHERE id=$id");
        echo '<p style="color: green; background: #dcfce7; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">✅ Announcement deleted!</p>';
      }
      ?>

      <div>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY pinned DESC, created_at DESC");
        while ($row = mysqli_fetch_assoc($result)):
        ?>
        <div class="card" style="margin-bottom: 1rem;">
          <h3><?php echo $row['title']; ?> <?php if($row['pinned']) echo '📌'; ?></h3>
          <p><?php echo $row['content']; ?></p>
          <small><?php echo $row['category']; ?> • <?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
          <div style="margin-top: 1rem;">
            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus announcement ini?')" class="btn btn-red">Delete</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <h1>Create New Announcement</h1>
      <div class="card">
        <form method="POST">
          <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required>
          </div>
          <div class="form-group">
            <label>Content *</label>
            <textarea name="content" required></textarea>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category">
              <option>Company</option>
              <option>Marketplace</option>
              <option>Workshop</option>
              <option>Farm</option>
              <option>Asian Food</option>
            </select>
          </div>
          <div class="form-group">
            <label><input type="checkbox" name="pinned"> Pin to Top</label>
          </div>
          <button class="btn btn-primary" style="width: 100%;">Create</button>
          <a href="announcements.php" class="btn" style="width: 100%; text-align: center; background: #e5e7eb; margin-top: 0.5rem;">Cancel</a>
        </form>
      </div>

      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $category = sanitize($_POST['category']);
        $pinned = isset($_POST['pinned']) ? 1 : 0;
        
        mysqli_query($conn, "INSERT INTO announcements (title, content, category, pinned, created_by) VALUES ('$title', '$content', '$category', $pinned, {$_SESSION['user_id']})");
        header('Location: announcements.php');
      }
      ?>
    <?php endif; ?>
  </div>
</section>

</body>
</html>
