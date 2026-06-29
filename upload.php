<?php
session_start();
require_once 'config/database.php';
require_once 'config/admin.php';

// Check if logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$isAdmin = ($username === ADMIN_USERNAME);

// Get current member data
$stmt = $conn->prepare("SELECT foto FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

$currentFoto = $member['foto'] ?? '';
$uploadSuccess = false;
$uploadError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $file = $_FILES['foto'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 20 * 1024 * 1024; // 20MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadError = 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.';
        } elseif ($file['size'] > $maxSize) {
            $uploadError = 'Ukuran file maksimal 20MB.';
        } else {
            // Folder upload absolut
            $uploadDir = __DIR__ . '/upload-foto/';
            
            // Buat folder otomatis jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Get file extension
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            // Auto name = username
            $newName = $username . '.' . $ext;
            $uploadPath = $uploadDir . $newName;
            
            // Delete old photo if exists
            if ($currentFoto && file_exists($uploadDir . $currentFoto)) {
                unlink($uploadDir . $currentFoto);
            }
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Update database
                $stmt = $conn->prepare("UPDATE members SET foto = ?, foto_uploaded_at = NOW() WHERE username = ?");
                $stmt->bind_param("ss", $newName, $username);
                $stmt->execute();
                $stmt->close();
                
                $currentFoto = $newName;
                $uploadSuccess = true;
            } else {
                $uploadError = 'Gagal mengupload file.';
            }
        }
    } else {
        $uploadError = 'Terjadi kesalahan saat upload.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Upload dokumen untuk pengumpulan MOTS IG">
    <title>Pengumpulan Dokumen - MOTS IG</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        input[type="file"]::file-selector-button {
            display: none;
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-primary': '#0a0a14',
                        'dark-secondary': '#10101e',
                        'dark-card': '#161628',
                        'dark-border': '#1e1c36',
                        'accent': '#9381ff',
                        'accent-hover': '#7b6be0',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="p-4 font-inter">
    
    <!-- Animated Background -->
    <div class="bg-scene" aria-hidden="true">
        <div class="bg-orb bg-orb--1"></div>
        <div class="bg-orb bg-orb--2"></div>
        <div class="bg-orb bg-orb--3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>
    
    <!-- Content -->
    <div class="content-wrapper max-w-md mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-6 animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold text-white"
                     style="background: linear-gradient(135deg, #9381ff, #b8b8ff);">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">Pengumpulan Dokumen</h1>
                    <p class="text-gray-500 text-xs font-medium">@<?= htmlspecialchars($username) ?></p>
                </div>
            </div>
            <div class="flex gap-2">
                <?php if ($isAdmin): ?>
                <a href="admin.php" class="px-3 py-2 glass-card text-sm text-accent hover:text-white transition-colors font-medium">
                    Admin
                </a>
                <?php endif; ?>
                <a href="?logout=1" class="px-3 py-2 glass-card text-sm text-gray-400 hover:text-white transition-colors">
                    Logout
                </a>
            </div>
        </header>
        
        <!-- Upload Card -->
        <div class="glass-card p-6 mb-5 animate-fade-in-up" style="animation-delay: 0.1s;">
            <?php if ($uploadSuccess): ?>
            <div class="alert-success px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Dokumen berhasil diupload!
            </div>
            <?php endif; ?>
            
            <?php if ($uploadError): ?>
            <div class="alert-error px-4 py-3 mb-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <?= htmlspecialchars($uploadError) ?>
            </div>
            <?php endif; ?>
            
            <!-- Current Document Preview -->
            <div class="text-center mb-5">
                <?php if ($currentFoto && file_exists(__DIR__ . '/upload-foto/' . $currentFoto)): ?>
                <div class="relative inline-block">
                    <img src="serve-file.php?file=<?= urlencode($currentFoto) ?>" 
                         alt="Dokumen" 
                         class="max-w-full h-auto max-h-48 rounded-xl mx-auto mb-3"
                         style="border: 1px solid rgba(147,129,255,0.2); box-shadow: 0 0 30px rgba(147,129,255,0.1);">
                </div>
                <div class="flex items-center justify-center gap-1.5 text-green-400 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Dokumen sudah terkirim
                </div>
                <?php else: ?>
                <div class="w-28 h-28 rounded-2xl mx-auto mb-3 flex items-center justify-center"
                     style="background: rgba(147,129,255,0.05); border: 1px solid rgba(147,129,255,0.1);">
                    <svg class="w-10 h-10 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-xs font-medium">Belum upload dokumen</p>
                <?php endif; ?>
            </div>
            
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data">
                <label class="drop-zone block w-full p-5 text-center cursor-pointer mb-3">
                    <input type="file" name="foto" accept="image/*" required class="hidden" id="fileInput" onchange="this.form.submit()">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span class="text-gray-400 text-sm font-medium block">Pilih dokumen</span>
                    <span class="text-gray-600 text-xs block mt-1">JPG, PNG, GIF, WebP — Maks 20MB</span>
                </label>
                <p class="text-gray-600 text-xs text-center">Nama file otomatis: <span class="text-accent font-medium"><?= htmlspecialchars($username) ?>.ext</span></p>
            </form>
        </div>
        
        <!-- Back link -->
        <div class="text-center animate-fade-in" style="animation-delay: 0.2s;">
            <a href="index.php" class="inline-flex items-center gap-1.5 text-gray-500 text-sm hover:text-accent transition-all duration-300 hover:translate-x-[-3px]">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"/>
                    <path d="M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke daftar member
            </a>
        </div>
    </div>
</body>
</html>
