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
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadError = 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.';
        } elseif ($file['size'] > $maxSize) {
            $uploadError = 'Ukuran file maksimal 5MB.';
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
                $stmt = $conn->prepare("UPDATE members SET foto = ? WHERE username = ?");
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
    <title>Pengumpulan Dokumen - MOTS IG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-primary: #0d1117;
            --bg-secondary: #161b22;
            --bg-card: #21262d;
            --text-primary: #f0f6fc;
            --text-secondary: #8b949e;
            --accent: #58a6ff;
            --accent-hover: #1f6feb;
            --border: #30363d;
        }
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            min-height: 100dvh;
            -webkit-font-smoothing: antialiased;
            -webkit-user-select: none;
        }
        input[type="file"]::file-selector-button {
            display: none;
        }
    </style>
</head>
<body class="p-4">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-white">Pengumpulan Dokumen</h1>
                <p class="text-gray-500 text-sm">@<?= htmlspecialchars($username) ?></p>
            </div>
            <div class="flex gap-2">
                <?php if ($isAdmin): ?>
                <a href="admin.php" class="px-3 py-2 bg-dark-secondary border border-dark-border rounded-lg text-sm text-white hover:bg-dark-border transition-colors">
                    Admin
                </a>
                <?php endif; ?>
                <a href="?logout=1" class="px-3 py-2 bg-dark-secondary border border-dark-border rounded-lg text-sm text-gray-400 hover:text-white transition-colors">
                    Logout
                </a>
            </div>
        </header>
        
        <!-- Upload Card -->
        <div class="bg-dark-secondary border border-dark-border rounded-xl p-5 mb-4">
            <?php if ($uploadSuccess): ?>
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-4 text-sm">
                Dokumen berhasil diupload!
            </div>
            <?php endif; ?>
            
            <?php if ($uploadError): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= htmlspecialchars($uploadError) ?>
            </div>
            <?php endif; ?>
            
            <!-- Current Document Preview -->
            <div class="text-center mb-4">
                <?php if ($currentFoto && file_exists(__DIR__ . '/upload-foto/' . $currentFoto)): ?>
                <img src="serve-file.php?file=<?= urlencode($currentFoto) ?>" 
                     alt="Dokumen" 
                     class="max-w-full h-auto max-h-48 rounded-lg mx-auto mb-3 border border-dark-border">
                <p class="text-green-400 text-xs">✓ Dokumen sudah terkirim</p>
                <?php else: ?>
                <div class="w-32 h-32 rounded-lg bg-dark-border mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-xs">Belum upload dokumen</p>
                <?php endif; ?>
            </div>
            
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data">
                <label class="block w-full border-2 border-dashed border-dark-border rounded-lg p-4 text-center cursor-pointer hover:border-accent transition-colors mb-3">
                    <input type="file" name="foto" accept="image/*" required class="hidden" id="fileInput" onchange="this.form.submit()">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span class="text-gray-400 text-sm">Pilih dokumen (JPG, PNG, GIF, WebP)</span>
                    <span class="text-gray-600 text-xs block mt-1">Maks 5MB</span>
                </label>
                <p class="text-gray-600 text-xs text-center">Nama file akan otomatis menjadi: <span class="text-accent"><?= htmlspecialchars($username) ?>.ext</span></p>
            </form>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="text-gray-500 text-sm hover:text-accent transition-colors">
                ← Kembali ke daftar member
            </a>
        </div>
    </div>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-primary': '#0d1117',
                        'dark-secondary': '#161b22',
                        'dark-border': '#30363d',
                        'accent': '#58a6ff',
                        'accent-hover': '#1f6feb',
                    }
                }
            }
        }
    </script>
</body>
</html>
