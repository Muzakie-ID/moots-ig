<?php
require_once 'config/database.php';

// Ambil semua member
$result = $conn->query("SELECT * FROM members ORDER BY created_at DESC");
$members = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0d1117">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Mots IG - Daftar Member</title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            primary: '#0d1117',
                            secondary: '#161b22',
                            card: '#21262d',
                            border: '#30363d',
                        },
                        accent: '#58a6ff',
                        'accent-hover': '#1f6feb',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-primary min-h-screen">
    
    <div class="w-full max-w-md mx-auto px-4 py-6">
        <!-- Header -->
        <header class="text-center mb-6 animate-fade-in">
            <h1 class="text-2xl font-bold text-white mb-1">Mots IG</h1>
            <p class="text-gray-500 text-sm">Kunjungi profile IG member kami</p>
        </header>
        
        <!-- List Member -->
        <section class="mb-6">
            <div id="memberList">
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $member): ?>
                        <a href="redirect.php?username=<?= urlencode($member['username']) ?>" 
                           class="flex items-center justify-between py-3 px-4 bg-dark-secondary rounded-lg mb-2 border border-dark-border 
                                  transition-all duration-200 hover:bg-dark-border hover:translate-x-1 active:scale-[0.98]">
                            <span class="text-white font-medium text-sm">@<?= htmlspecialchars($member['username']) ?></span>
                            <span class="flex items-center gap-1 text-accent text-xs font-medium">
                                Kunjungi
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12 text-gray-500 animate-fade-in">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <p class="text-sm">Belum ada member</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Form Tambah -->
        <section>
            <div class="bg-dark-card border border-dark-border rounded-xl p-4">
                <h2 class="text-white font-semibold text-base mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    Tambah Member
                </h2>
                <form id="addForm">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Username Instagram" 
                            autocomplete="off"
                            required
                            class="flex-1 bg-dark-secondary border border-dark-border rounded-lg px-4 py-3 text-white text-sm
                                   placeholder-gray-500"
                        >
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="btn-add"
                        >
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    
    <!-- JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
