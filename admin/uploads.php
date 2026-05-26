<?php
include '../includes/config.php';
requireAdmin();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid security token.';
        header('Location: uploads.php');
        exit();
    }

    if ($_POST['action'] === 'delete_file') {
        $file_path_input = $_POST['file_path'] ?? '';
        $uploads_dir = realpath(__DIR__ . '/../assets/uploads');

        // Build full path: uploads_dir + file_path (e.g. /uploads/filename.png)
        $full_path = $uploads_dir . '/' . ltrim($file_path_input, '/');

        if (is_file($full_path)) {
            if (unlink($full_path)) {
                $_SESSION['success'] = 'File deleted: ' . basename($full_path);
            } else {
                $_SESSION['error'] = 'Failed to delete file.';
            }
        } else {
            $_SESSION['error'] = 'Invalid file path.';
        }
        header('Location: uploads.php');
        exit();
    }

    if ($_POST['action'] === 'delete_multiple') {
        $files = $_POST['files'] ?? [];
        $uploads_dir = realpath(__DIR__ . '/../assets/uploads');
        $deleted = 0;

        foreach ($files as $file_path_input) {
            $full_path = $uploads_dir . '/' . ltrim($file_path_input, '/');
            if (is_file($full_path)) {
                unlink($full_path);
                $deleted++;
            }
        }
        $_SESSION['success'] = "Deleted $deleted file(s)!";
        header('Location: uploads.php');
        exit();
    }
}

// Get all files in uploads directory
function getDirContents($dir, $relativePath = '') {
    $files = [];
    if (!is_dir($dir)) return $files;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $dir . '/' . $item;
        $relPath = ltrim($relativePath . '/' . $item, '/');

        if (is_dir($fullPath)) {
            $files = array_merge($files, getDirContents($fullPath, $relPath));
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];

            if (in_array($ext, $image_exts)) {
                $files[] = [
                    'name' => $item,
                    'path' => $relPath,
                    'size' => filesize($fullPath),
                    'modified' => filemtime($fullPath),
                    'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico'])
                ];
            }
        }
    }
    return $files;
}

$uploads_path = '../assets/uploads';
$all_files = getDirContents($uploads_path);

// Sort by modified date (newest first)
usort($all_files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

$total_size = array_sum(array_column($all_files, 'size'));
$total_size_formatted = $total_size > 1048576 ? round($total_size / 1048576, 2) . ' MB' : round($total_size / 1024, 2) . ' KB';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Uploads - Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .upload-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .stats-box {
      background: linear-gradient(135deg, #1e40af, #3b82f6);
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      display: flex;
      gap: 20px;
    }
    .stat-item {
      text-align: center;
    }
    .stat-value {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .stat-label {
      font-size: 0.8rem;
      opacity: 0.9;
    }
    .upload-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 1.5rem;
    }
    .upload-card {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.2s;
      display: flex;
      flex-direction: column;
      height: 280px;
    }
    .upload-card:hover {
      border-color: #3b82f6;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }
    .upload-thumb {
      width: 100%;
      height: 150px;
      object-fit: cover;
      background: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .upload-thumb-placeholder {
      width: 100%;
      height: 150px;
      background: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: #9ca3af;
      flex-shrink: 0;
    }
    .upload-info {
      padding: 12px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }
    .upload-top {
      margin-bottom: auto;
    }
    .upload-name {
      font-size: 0.8rem;
      font-weight: 500;
      color: #374151;
      word-break: break-word;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      line-height: 1.4;
      margin-bottom: 4px;
      flex-shrink: 0;
      min-height: 2.8em;
    }
    .upload-meta {
      font-size: 0.75rem;
      color: #6b7280;
      margin-bottom: 8px;
      flex-shrink: 0;
    }
    .upload-actions {
      margin-top: auto;
      padding-top: 8px;
    }
    .upload-actions form {
      width: 100%;
    }
    .btn-delete {
      width: 100%;
      height: 36px;
      padding: 0;
      background: #fee2e2;
      color: #dc2626;
      border: none;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
    }
    .btn-delete:hover {
      background: #fecaca;
    }
    .select-checkbox {
      position: absolute;
      top: 8px;
      left: 8px;
      width: 20px;
      height: 20px;
      cursor: pointer;
    }
    .bulk-actions {
      display: none;
      background: #fee2e2;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 1rem;
      align-items: center;
      gap: 12px;
    }
    .bulk-actions.show {
      display: flex;
    }
    .btn-bulk-delete {
      padding: 8px 16px;
      background: #dc2626;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
    }
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    .empty-state svg {
      width: 80px;
      height: 80px;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
    .card-checkbox {
      position: relative;
    }
    .folder-badge {
      background: #3b82f6;
      color: white;
      font-size: 0.7rem;
      padding: 2px 6px;
      border-radius: 4px;
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
      <a href="users.php" class="navbar-btn" style="background: white; color: #1e40af; margin-right: 0.5rem;">👥 Users</a>
      <a href="../" class="navbar-btn">Home</a>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    <div class="upload-header">
      <h1>🗂️ File Uploads Manager</h1>
      <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        ✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        ✗ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-box">
      <div class="stat-item">
        <div class="stat-value"><?php echo count($all_files); ?></div>
        <div class="stat-label">Total Files</div>
      </div>
      <div class="stat-item">
        <div class="stat-value"><?php echo $total_size_formatted; ?></div>
        <div class="stat-label">Total Size</div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="bulk-actions">
      <span id="selectedCount">0 selected</span>
      <form method="POST" id="bulkForm" style="display: inline;">
        <?php echo getCSRFField(); ?>
        <input type="hidden" name="action" value="delete_multiple">
        <div id="selectedFiles"></div>
        <button type="submit" class="btn-bulk-delete" onclick="return confirm('Delete selected files? This cannot be undone.');">
          🗑️ Delete Selected
        </button>
      </form>
      <button onclick="clearSelection()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">Cancel</button>
    </div>

    <!-- File Grid -->
    <?php if (count($all_files) > 0): ?>
      <form method="POST" id="mainForm">
        <?php echo getCSRFField(); ?>
        <input type="hidden" name="action" value="delete_multiple">
        <input type="hidden" name="files" id="filesInput" value="">
        <div class="upload-grid">
          <?php foreach ($all_files as $file): ?>
            <div class="upload-card card-checkbox">
              <input type="checkbox" class="select-checkbox file-checkbox"
                     value="<?php echo htmlspecialchars($file['path']); ?>"
                     onchange="updateBulkActions()">
              <?php if ($file['is_image']): ?>
                <img src="../assets/uploads/<?php echo ltrim($file['path'], '/'); ?>"
                     alt="<?php echo htmlspecialchars($file['name']); ?>"
                     class="upload-thumb"
                     onerror="this.outerHTML='<div class=upload-thumb-placeholder>🖼️</div>'">
              <?php else: ?>
                <div class="upload-thumb-placeholder">📄</div>
              <?php endif; ?>
              <div class="upload-info">
                <div class="upload-top">
                  <div class="upload-name"><?php echo htmlspecialchars($file['name']); ?></div>
                  <div class="upload-meta">
                    <?php echo round($file['size'] / 1024, 1); ?> KB
                    &bull;
                    <?php echo date('d M Y', $file['modified']); ?>
                  </div>
                </div>
                <div class="upload-actions">
                  <form method="POST" onsubmit="return confirm('Delete this file? This cannot be undone.');">
                    <?php echo getCSRFField(); ?>
                    <input type="hidden" name="action" value="delete_file">
                    <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file['path']); ?>">
                    <button type="submit" class="btn-delete">🗑️ Delete</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </form>
    <?php else: ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M3 7v14a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
        </svg>
        <h3>No files found</h3>
        <p>There are no image files in the uploads folder.</p>
      </div>
    <?php endif; ?>

  </div>
</section>

<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.file-checkbox:checked');
    const count = checkboxes.length;
    const bulkDiv = document.getElementById('bulkActions');
    const countSpan = document.getElementById('selectedCount');
    const filesInput = document.getElementById('filesInput');

    if (count > 0) {
        bulkDiv.classList.add('show');
        countSpan.textContent = count + ' selected';

        // Collect all selected file paths
        const paths = [];
        checkboxes.forEach(cb => paths.push(cb.value));
        filesInput.value = JSON.stringify(paths);
    } else {
        bulkDiv.classList.remove('show');
    }
}

function clearSelection() {
    document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
    updateBulkActions();
}

// Select all functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add shift-click for range selection could be added here
});
</script>

</body>
</html>