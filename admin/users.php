<?php
include '../includes/config.php';
requireAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token. Please refresh the page.';
            header('Location: users.php');
            exit();
        }

        $user_id = (int)$_POST['user_id'];

        switch ($_POST['action']) {
            case 'update_role':
                $new_role = sanitize($_POST['role']);
                if (in_array($new_role, ['admin', 'employee', 'user'])) {
                    mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = $user_id");
                    $_SESSION['success'] = 'Role updated successfully!';
                }
                break;

            case 'delete_user':
                // Prevent self-deletion
                if ($user_id !== $_SESSION['user_id']) {
                    mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
                    $_SESSION['success'] = 'User deleted successfully!';
                }
                break;
        }

        header('Location: users.php');
        exit();
    }
}

// Get users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
  <div class="navbar-container">
    <div class="navbar-logo">🔑 Admin Panel</div>
    <div>
      <a href="index.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">Dashboard</a>
      <a href="featured-marketplace.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">⭐ Featured</a>
      <a href="announcements.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">📢 Announcements</a>
      <a href="../" class="navbar-btn">Home</a>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h1>👥 Manage Users</h1>
      <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        ✗ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <table style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background: #f3f4f6; text-align: left;">
            <th style="padding: 12px;">ID</th>
            <th style="padding: 12px;">Username</th>
            <th style="padding: 12px;">Email</th>
            <th style="padding: 12px;">Role</th>
            <th style="padding: 12px;">Registered</th>
            <th style="padding: 12px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($user = mysqli_fetch_assoc($users)): ?>
            <?php
            $role_colors = [
              'admin' => '#dc2626',
              'employee' => '#2563eb',
              'user' => '#6b7280'
            ];
            $role_color = $role_colors[$user['role']] ?? '#6b7280';
            ?>
            <tr style="border-bottom: 1px solid #e5e7eb;">
              <td style="padding: 12px;">#<?php echo $user['id']; ?></td>
              <td style="padding: 12px;">
                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
              </td>
              <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
              <td style="padding: 12px;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
              <td style="padding: 12px;">
                <form action="" method="POST" style="display: flex; gap: 8px; align-items: center;">
                  <?php echo getCSRFField(); ?>
                  <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                  <select name="role" onchange="this.form.submit()" style="
                    padding: 4px 8px;
                    border-radius: 4px;
                    border: 1px solid #d1d5db;
                    background: <?php echo $role_color; ?>;
                    color: white;
                    font-weight: bold;
                    cursor: pointer;
                  ">
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="employee" <?php echo $user['role'] === 'employee' ? 'selected' : ''; ?>>Staff</option>
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                  </select>
                  <input type="hidden" name="action" value="update_role">
                </form>
              </td>
              <td style="padding: 12px;">
                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                    <form action="" method="POST" onsubmit="return confirm('Delete this user? This cannot be undone.');" style="display: inline;">
                      <?php echo getCSRFField(); ?>
                      <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                      <input type="hidden" name="action" value="delete_user">
                      <button type="submit" class="btn btn-small" style="background: #dc2626; color: white;">Delete</button>
                    </form>
                  <?php else: ?>
                    <span style="color: #6b7280; font-style: italic;">(You)</span>
                  <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <?php if (mysqli_num_rows($users) === 0): ?>
        <p style="text-align: center; padding: 2rem; color: #6b7280;">No users found.</p>
      <?php endif; ?>
    </div>

    <div class="card" style="margin-top: 1.5rem; background: #f0f9ff; border-left: 4px solid #2563eb;">
      <h3 style="color: #1e40af;">📋 Role Guide</h3>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 0.5rem;">
        <div>
          <strong style="color: #dc2626;">🔴 Admin</strong>
          <p style="margin: 0.5rem 0; font-size: 0.9rem; color: #374151;">
            Full access to all admin features including user management, marketplace, announcements, and VIP featured posts.
          </p>
        </div>
        <div>
          <strong style="color: #2563eb;">🔵 Staff</strong>
          <p style="margin: 0.5rem 0; font-size: 0.9rem; color: #374151;">
            Can manage marketplace posts, announcements, and featured content. Cannot manage other users.
          </p>
        </div>
        <div>
          <strong style="color: #6b7280;">⚪ User</strong>
          <p style="margin: 0.5rem 0; font-size: 0.9rem; color: #374151;">
            Regular user access to marketplace posting and browsing. No admin panel access.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>