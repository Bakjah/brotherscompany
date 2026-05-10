<?php include '../includes/config.php'; requireAdmin(); 

// Handle featured action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $action = $_POST['action'];
    $duration_days = intval($_POST['duration_days'] ?? 7);
    
    if ($action === 'feature') {
        // Set featured dengan expiry date
        $expiry = date('Y-m-d H:i:s', strtotime("+$duration_days days"));
        mysqli_query($conn, "UPDATE marketplace_posts SET is_featured = 1, featured_expiry = '$expiry' WHERE id = $post_id");
        $message = "✅ Marketplace featured selama $duration_days hari!";
    } elseif ($action === 'unfeature') {
        // Remove featured
        mysqli_query($conn, "UPDATE marketplace_posts SET is_featured = 0, featured_expiry = NULL WHERE id = $post_id");
        $message = "❌ Featured dihapus";
    }
}

// Ambil all marketplace posts
$result = mysqli_query($conn, "
    SELECT mp.*, u.username, u.email,
           CASE 
               WHEN mp.featured_expiry > NOW() THEN 'active'
               WHEN mp.featured_expiry IS NOT NULL THEN 'expired'
               ELSE 'not_featured'
           END as featured_status
    FROM marketplace_posts mp
    LEFT JOIN users u ON mp.user_id = u.id
    ORDER BY mp.is_featured DESC, mp.created_at DESC
");

// Ambil featured marketplace (yang masih aktif)
$featuredResult = mysqli_query($conn, "
    SELECT * FROM marketplace_posts 
    WHERE is_featured = 1 AND featured_expiry > NOW()
    ORDER BY featured_expiry ASC
");
$featured_count = mysqli_num_rows($featuredResult);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Marketplace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .featured-badge {
            display: inline-block;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 0.5px solid #e5e7eb;
            border-radius: 0.5rem;
        }

        th {
            background: #f3f4f6;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }

        tr:hover {
            background: #fafafa;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-not {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-feature {
            background: #fbbf24;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-feature:hover {
            background: #f59e0b;
        }

        .btn-unfeature {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-unfeature:hover {
            background: #dc2626;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 500px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 0.5px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 14px;
        }

        .btn-submit {
            background: #fbbf24;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #f59e0b;
        }

        .btn-cancel {
            background: #6b7280;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            margin-top: 0.5rem;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #4b5563;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border: 0.5px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #fbbf24;
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav class="navbar" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
    <div class="navbar-container">
        <div class="navbar-logo">⭐ Featured Marketplace</div>
        <a href="index.php" class="navbar-btn">← Back</a>
    </div>
</nav>

<section class="section">
    <div class="container">
        <h1>⭐ Kelola Featured Marketplace</h1>

        <?php if(isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="info-box">
            💡 <strong>Fitur Featured:</strong> Admin bisa menampilkan marketplace member VIP di halaman teratas sebagai iklan premium. Member VIP akan mendapatkan lebih banyak visibility untuk menjual barang mereka.
        </div>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Posts</div>
                <div class="stat-number"><?php echo mysqli_num_rows($result); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Featured Active</div>
                <div class="stat-number"><?php echo $featured_count; ?></div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Penjual</th>
                        <th>Status</th>
                        <th>Expiry</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($result, 0);
                    while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo $row['nama_barang']; ?></strong>
                            <?php if($row['is_featured'] && $row['featured_status'] === 'active'): ?>
                                <span class="featured-badge">⭐ FEATURED</span>
                            <?php endif; ?>
                            <br>
                            <small style="color: #999;"><?php echo $row['jenis_barang']; ?></small>
                        </td>
                        <td>
                            <strong><?php echo $row['username']; ?></strong>
                            <br>
                            <small style="color: #999;"><?php echo $row['email']; ?></small>
                        </td>
                        <td>
                            <?php if($row['featured_status'] === 'active'): ?>
                                <span class="status-badge status-active">✅ Active</span>
                            <?php elseif($row['featured_status'] === 'expired'): ?>
                                <span class="status-badge status-expired">⏰ Expired</span>
                            <?php else: ?>
                                <span class="status-badge status-not">⭕ Not Featured</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['featured_expiry']): ?>
                                <?php echo date('d M Y H:i', strtotime($row['featured_expiry'])); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <?php if($row['featured_status'] !== 'active'): ?>
                                    <button class="btn-feature" onclick="openFeatureModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nama_barang']); ?>')">
                                        ⭐ Feature
                                    </button>
                                <?php else: ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="unfeature">
                                        <button type="submit" class="btn-unfeature" onclick="return confirm('Hapus featured?');">
                                            ✕ Unfeature
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Feature Modal -->
<div id="featureModal" class="modal">
    <div class="modal-content">
        <div class="modal-title">⭐ Feature Marketplace</div>
        
        <form method="POST">
            <input type="hidden" name="post_id" id="post_id">
            <input type="hidden" name="action" value="feature">

            <div class="form-group">
                <label>Nama Barang:</label>
                <input type="text" id="barang_name" readonly style="background: #f3f4f6;">
            </div>

            <div class="form-group">
                <label>Durasi Feature (hari):</label>
                <select name="duration_days" required>
                    <option value="1">1 Hari</option>
                    <option value="3">3 Hari</option>
                    <option value="7" selected>7 Hari (Standar)</option>
                    <option value="14">14 Hari</option>
                    <option value="30">30 Hari</option>
                </select>
            </div>

            <div style="background: #f0f9ff; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 13px;">
                <strong>💰 VIP Package:</strong><br>
                - 7 Hari: Rp 50.000<br>
                - 14 Hari: Rp 90.000<br>
                - 30 Hari: Rp 150.000<br>
                <em>*Harga dapat disesuaikan</em>
            </div>

            <button type="submit" class="btn-submit">✅ Konfirmasi Feature</button>
            <button type="button" class="btn-cancel" onclick="closeFeatureModal()">Batal</button>
        </form>
    </div>
</div>

<script>
function openFeatureModal(postId, barangName) {
    document.getElementById('post_id').value = postId;
    document.getElementById('barang_name').value = barangName;
    document.getElementById('featureModal').style.display = 'block';
}

function closeFeatureModal() {
    document.getElementById('featureModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('featureModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

</body>
</html>
