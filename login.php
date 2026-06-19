<?php
session_start();
require_once 'config/database.php';

// Check if already logged in
if (isset($_SESSION['username'])) {
    header('Location: upload.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    
    if (empty($username)) {
        $error = 'Username tidak boleh kosong';
    } else {
        // Check if username exists in members
        $stmt = $conn->prepare("SELECT id FROM members WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['username'] = $username;
            header('Location: upload.php');
            exit;
        } else {
            $error = 'Username tidak ditemukan. Harus terdaftar sebagai member.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login - MOTS IG</title>
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
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white mb-2">MOTS IG</h1>
            <p class="text-gray-500 text-sm">Login dengan username member</p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username Instagram"
                    autocomplete="off"
                    autofocus
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
