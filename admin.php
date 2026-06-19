<?php
session_start();
require_once 'config/database.php';
require_once 'config/admin.php';

// Handle Admin Login
$loginError = '';
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = 'Username atau password salah';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Require login for rest of page
if (!$isLoggedIn) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <title>Admin Login - MOTS IG</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            :root {
                --bg-primary: #0d1117;
                --bg-secondary: #161b22;
                --text-primary: #f0f6fc;
                --accent: #58a6ff;
                --border: #30363d;
            }
            body {
                background-color: var(--bg-primary);
                color: var(--text-primary);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                min-height: 100vh;
                min-height: 100dvh;
            }
        </style>
    </head>
    <body class="flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white mb-2">Admin Login</h1>
                <p class="text-gray-500 text-sm">MOTS IG Management</p>
            </div>
            
            <?php if ($loginError): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= htmlspecialchars($loginError) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="admin_login" value="1">
                <div>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Username"
                        autocomplete="off"
                        autofocus
                        class="w-full bg-dark-secondary border border-dark-border rounded-lg px-4 py-3 text-white text-sm
                               placeholder-gray-500 outline-none focus:border-accent transition-colors"
                    >
                </div>
                <div>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        autocomplete="off"
                        class="w-full bg-dark-secondary border border-dark-border rounded-lg px-4 py-3 text-white text-sm
                               placeholder-gray-500 outline-none focus:border-accent transition-colors"
                    >
                </div>
                <button 
                    type="submit"
                    class="w-full bg-accent hover:bg-accent-hover text-white font-medium py-3 rounded-lg transition-colors"
                >
                    Login
                </button>
            </form>
            
            <div class="mt-6 text-center">
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
    <?php
    exit;
}

// Logged in - Show Admin Dashboard
$adminUsername = $_SESSION['admin_username'];

// Handle photo deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_foto'])) {
    $memberId = (int)$_POST['member_id'];
    $fotoName = $_POST['foto_name'];
    
    // Delete file
    if ($fotoName && file_exists('upload-foto/' . $fotoName)) {
        unlink('upload-foto/' . $fotoName);
    }
    
    // Update database
    $stmt = $conn->prepare("UPDATE members SET foto = NULL WHERE id = ?");
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $stmt->close();
    
    header('Location: admin.php');
    exit;
}

// Get all members with upload status
$result = $conn->query("SELECT id, username, foto, created_at FROM members ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Admin - MOTS IG</title>
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
        }
    </style>
</head>
<body class="p-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-gray-500 text-sm">@<?= htmlspecialchars($adminUsername) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="?logout=1" class="px-3 py-2 bg-dark-secondary border border-dark-border rounded-lg text-sm text-gray-400 hover:text-white transition-colors">
                    Logout
                </a>
            </div>
        </header>
        
        <!-- Stats -->
        <?php 
        $totalMembers = $result->num_rows;
        $uploadedCount = 0;
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            if ($row['foto']) $uploadedCount++;
        }
        $result->data_seek(0);
        ?>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-dark-secondary border border-dark-border rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-accent"><?= $totalMembers ?></p>
                <p class="text-gray-500 text-sm">Total Member</p>
            </div>
            <div class="bg-dark-secondary border border-dark-border rounded-xl p-4 text-center">
                <p class="text-3xl font-bold text-green-400"><?= $uploadedCount ?></p>
                <p class="text-gray-500 text-sm">Sudah Upload</p>
            </div>
        </div>
        
        <div class="bg-dark-secondary border border-dark-border rounded-xl p-4 mb-4">
            <p class="text-gray-500 text-sm">
                <?= $totalMembers - $uploadedCount ?> member belum upload dokumen
            </p>
        </div>
        
        <!-- Member List -->
        <h2 class="text-white font-medium mb-3">Status Dokumen Member</h2>
        <div class="space-y-2">
            <?php while ($member = $result->fetch_assoc()): ?>
            <div class="flex items-center justify-between bg-dark-secondary border border-dark-border rounded-lg p-3">
                <div class="flex items-center gap-3">
                    <?php if ($member['foto'] && file_exists('upload-foto/' . $member['foto'])): ?>
                    <img src="upload-foto/<?= htmlspecialchars($member['foto']) ?>" 
                         alt="" 
                         class="w-12 h-12 rounded-lg object-cover border border-dark-border">
                    <div>
                        <p class="text-white text-sm font-medium">@<?= htmlspecialchars($member['username']) ?></p>
                        <p class="text-green-400 text-xs">✓ <?= htmlspecialchars($member['foto']) ?></p>
                    </div>
                    <?php else: ?>
                    <div class="w-12 h-12 rounded-lg bg-dark-border flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">@<?= htmlspecialchars($member['username']) ?></p>
                        <p class="text-gray-500 text-xs">Belum upload</p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($member['foto']): ?>
                <div class="flex items-center gap-2">
                    <a href="serve-file.php?file=<?= urlencode($member['foto']) ?>" 
                       class="text-accent text-xs hover:underline">Download</a>
                    <form method="POST" onsubmit="return confirm('Hapus dokumen @<?= htmlspecialchars($member['username']) ?>?')">
                        <input type="hidden" name="delete_foto" value="1">
                        <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                        <input type="hidden" name="foto_name" value="<?= htmlspecialchars($member['foto']) ?>">
                        <button type="submit" class="text-red-400 text-xs hover:underline">Hapus</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="mt-6 text-center">
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
