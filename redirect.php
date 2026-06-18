<?php
if (isset($_GET['username']) && !empty($_GET['username'])) {
    $username = trim($_GET['username']);
    $username = str_replace('@', '', $username);
    $username = preg_replace('/\s+/', '', $username);
    
    if (!empty($username)) {
        // Redirect ke Instagram
        $instagram_url = "https://www.instagram.com/" . urlencode($username);
        header("Location: " . $instagram_url);
        exit;
    }
}

// Kalau username kosong, redirect ke halaman utama
header("Location: index.php");
exit;
