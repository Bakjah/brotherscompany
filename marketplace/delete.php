<?php
include '../includes/config.php';
requireLogin();

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Cek apakah post milik user ini
    $checkResult = mysqli_query($conn, "SELECT id, foto_barang FROM marketplace_posts WHERE id = $post_id AND user_id = $user_id");
    
    if (mysqli_num_rows($checkResult) > 0) {
        $row = mysqli_fetch_assoc($checkResult);
        
        // Hapus file foto jika ada
        if ($row['foto_barang']) {
            $filepath = '../assets/uploads/' . $row['foto_barang'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        
        // Hapus dari database
        mysqli_query($conn, "DELETE FROM marketplace_posts WHERE id = $post_id AND user_id = $user_id");
        
        // Redirect dengan success message
        header('Location: my-posts.php?success=1');
        exit();
    } else {
        // Post tidak ditemukan atau bukan milik user
        header('Location: my-posts.php?error=1');
        exit();
    }
} else {
    header('Location: my-posts.php');
    exit();
}
?>
