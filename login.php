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
    <meta name="description" content="Login ke MOTS IG dengan username member terdaftar">
    <title>Login - MOTS IG</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
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
<body class="flex items-center justify-center p-4 font-inter">
    
    <!-- Animated Background -->
    <div class="bg-scene" aria-hidden="true">
        <div class="bg-orb bg-orb--1"></div>
        <div class="bg-orb bg-orb--2"></div>
        <div class="bg-orb bg-orb--3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>
    
    <!-- Content -->
    <div class="content-wrapper w-full max-w-sm">
        <!-- Logo / Title -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center neon-glow" 
                 style="background: linear-gradient(135deg, rgba(147,129,255,0.15), rgba(184,184,255,0.15)); border: 1px solid rgba(147,129,255,0.2);">
                <svg class="w-7 h-7 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold gradient-text mb-2">MOTS IG</h1>
            <p class="text-gray-500 text-sm font-light">Login dengan username member</p>
        </div>
        
        <!-- Error message -->
        <?php if ($error): ?>
        <div class="alert-error px-4 py-3 mb-4 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.1s;">
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 font-medium mb-2 uppercase tracking-wider">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Masukkan username Instagram"
                        autocomplete="off"
                        autofocus
                        class="w-full px-4 py-3 text-white text-sm placeholder-gray-500 outline-none"
                    >
                </div>
                <button 
                    type="submit"
                    class="w-full btn-gradient py-3 rounded-xl text-sm"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Login
                    </span>
                </button>
            </form>
        </div>
        
        <!-- Back link -->
        <div class="mt-6 text-center animate-fade-in" style="animation-delay: 0.2s;">
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
