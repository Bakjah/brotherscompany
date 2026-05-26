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
                    // Delete all related data first (to avoid FK constraint issues)
                    mysqli_query($conn, "DELETE FROM marketplace_posts WHERE user_id = $user_id");
                    mysqli_query($conn, "DELETE FROM announcements WHERE created_by = $user_id");

                    // Then delete the user
                    mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
                    $_SESSION['success'] = 'User and all related data deleted successfully!';
                }
                break;
        }

        header('Location: users.php');
        exit();
    }
}

// Handle search and filter
$search = sanitize($_GET['search'] ?? '');
$filter_role = sanitize($_GET['role'] ?? '');

$where = [];
if ($search) {
    $where[] = "(username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%')";
}
if ($filter_role) {
    if ($filter_role === 'deleted') {
        $where[] = "username LIKE 'deleted_%'";
    } else {
        $where[] = "role = '$filter_role' AND username NOT LIKE 'deleted_%'";
    }
} else {
    // Default: hide deleted users from main list
    $where[] = "username NOT LIKE 'deleted_%'";
}

$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$users = mysqli_query($conn, "SELECT * FROM users $where_sql ORDER BY created_at DESC");
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .search-bar {
      display: flex;
      gap: 12px;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    .search-input {
      flex: 1;
      min-width: 250px;
      padding: 10px 16px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: border-color 0.2s;
    }
    .search-input:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .filter-select {
      padding: 10px 16px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      background: white;
      cursor: pointer;
      min-width: 150px;
    }
    .search-btn {
      padding: 10px 20px;
      background: #3b82f6;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      font-weight: 500;
    }
    .search-btn:hover {
      background: #2563eb;
    }
    .clear-btn {
      padding: 10px 20px;
      background: #6b7280;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
    }
    .user-count {
      background: #f3f4f6;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 14px;
      color: #6b7280;
      display: flex;
      align-items: center;
    }
    .user-count strong {
      color: #1e40af;
      margin-left: 4px;
    }
  </style>
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

    <!-- Search Bar -->
    <form method="GET" class="search-bar">
      <input type="text" name="search" class="search-input" placeholder="🔍 Search username, email, or name..." value="<?php echo htmlspecialchars($search); ?>">
      <select name="role" class="filter-select">
        <option value="">All Users</option>
        <option value="admin" <?php echo $filter_role === 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="employee" <?php echo $filter_role === 'employee' ? 'selected' : ''; ?>>Staff</option>
        <option value="user" <?php echo $filter_role === 'user' ? 'selected' : ''; ?>>User</option>
        <option value="deleted" <?php echo $filter_role === 'deleted' ? 'selected' : ''; ?>>🗑️ Deleted</option>
      </select>
      <button type="submit" class="search-btn">Search</button>
      <?php if ($search || $filter_role): ?>
        <a href="users.php" class="clear-btn">✕ Clear</a>
      <?php endif; ?>
      <div class="user-count">
        Total: <strong><?php echo mysqli_num_rows($users); ?></strong> / <?php echo $total_users; ?>
      </div>
    </form>

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
          <?php if (mysqli_num_rows($users) > 0): ?>
            <?php while ($user = mysqli_fetch_assoc($users)): ?>
              <?php
              $role_colors = [
                'admin' => '#dc2626',
                'employee' => '#2563eb',
                'user' => '#6b7280'
              ];
              $role_color = $role_colors[$user['role']] ?? '#6b7280';
              $role_label = $user['role'] === 'employee' ? 'Staff' : ucfirst($user['role']);
              ?>
              <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">#<?php echo $user['id']; ?></td>
                <td style="padding: 12px;">
                  <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                  <?php if (!empty($user['full_name'])): ?>
                    <br><small style="color: #6b7280;"><?php echo htmlspecialchars($user['full_name']); ?></small>
                  <?php endif; ?>
                </td>
                <td style="padding: 12px;"><?php echo htmlspecialchars($user['email']); ?></td>
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
                <td style="padding: 12px;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
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
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 3rem; color: #6b7280;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                <p>No users found matching your search.</p>
                <?php if ($search || $filter_role): ?>
                  <a href="users.php" style="color: #3b82f6;">Clear filters</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
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