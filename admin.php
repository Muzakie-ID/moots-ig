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
        <meta name="description" content="Admin login untuk MOTS IG Management">
        <title>Admin Login - MOTS IG</title>
        
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
            <div class="text-center mb-8 animate-fade-in">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center neon-glow" 
                     style="background: linear-gradient(135deg, rgba(147,129,255,0.15), rgba(184,184,255,0.15)); border: 1px solid rgba(147,129,255,0.2);">
                    <svg class="w-7 h-7 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold gradient-text mb-2">Admin Login</h1>
                <p class="text-gray-500 text-sm font-light">MOTS IG Management</p>
            </div>
            
            <?php if ($loginError): ?>
            <div class="alert-error px-4 py-3 mb-4 text-sm">
                <?= htmlspecialchars($loginError) ?>
            </div>
            <?php endif; ?>
            
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="admin_login" value="1">
                    <div>
                        <label class="block text-xs text-gray-500 font-medium mb-2 uppercase tracking-wider">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            placeholder="Username admin"
                            autocomplete="off"
                            autofocus
                            class="w-full px-4 py-3 text-white text-sm placeholder-gray-500 outline-none"
                        >
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 font-medium mb-2 uppercase tracking-wider">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            placeholder="Password admin"
                            autocomplete="off"
                            class="w-full px-4 py-3 text-white text-sm placeholder-gray-500 outline-none"
                        >
                    </div>
                    <button 
                        type="submit"
                        class="w-full btn-gradient py-3 rounded-xl text-sm"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/>
                            </svg>
                            Login
                        </span>
                    </button>
                </form>
            </div>
            
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
$result = $conn->query("SELECT id, username, foto, foto_uploaded_at, created_at FROM members ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="Admin dashboard MOTS IG - Kelola member dan dokumen">
    <title>Admin Dashboard - MOTS IG</title>
    
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
<body class="p-4 font-inter">
    
    <!-- Animated Background -->
    <div class="bg-scene" aria-hidden="true">
        <div class="bg-orb bg-orb--1"></div>
        <div class="bg-orb bg-orb--2"></div>
        <div class="bg-orb bg-orb--3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>
    
    <!-- Content -->
    <div class="content-wrapper max-w-2xl mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-6 animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" 
                     style="background: linear-gradient(135deg, #9381ff, #b8b8ff);">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">Admin Dashboard</h1>
                    <p class="text-gray-500 text-xs font-medium">@<?= htmlspecialchars($adminUsername) ?></p>
                </div>
            </div>
            <a href="?logout=1" class="px-4 py-2 glass-card text-sm text-gray-400 hover:text-white transition-all duration-300 hover:border-red-500/30">
                Logout
            </a>
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
        <div class="grid grid-cols-3 gap-3 mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="stat-card p-4 text-center">
                <p class="text-3xl font-extrabold stat-number"><?= $totalMembers ?></p>
                <p class="text-gray-500 text-xs mt-1 font-medium">Total Member</p>
            </div>
            <div class="stat-card p-4 text-center">
                <p class="text-3xl font-extrabold text-green-400"><?= $uploadedCount ?></p>
                <p class="text-gray-500 text-xs mt-1 font-medium">Sudah Upload</p>
            </div>
            <div class="stat-card p-4 text-center">
                <p class="text-3xl font-extrabold text-amber-400"><?= $totalMembers - $uploadedCount ?></p>
                <p class="text-gray-500 text-xs mt-1 font-medium">Belum Upload</p>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="glass-card p-4 mb-6 animate-fade-in-up" style="animation-delay: 0.15s;">
            <div class="flex items-center justify-between mb-2">
                <p class="text-gray-400 text-xs font-medium">Progress Upload</p>
                <p class="text-accent text-xs font-bold"><?= $totalMembers > 0 ? round(($uploadedCount / $totalMembers) * 100) : 0 ?>%</p>
            </div>
            <div class="w-full h-2 rounded-full overflow-hidden" style="background: rgba(147,129,255,0.1);">
                <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                     style="width: <?= $totalMembers > 0 ? round(($uploadedCount / $totalMembers) * 100) : 0 ?>%; background: linear-gradient(90deg, #9381ff, #b8b8ff);"></div>
            </div>
        </div>
        
        <!-- Member List -->
        <div class="flex items-center justify-between mb-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <h2 class="text-white font-semibold text-sm">Status Dokumen Member</h2>
            <span class="text-xs text-gray-500"><?= $totalMembers ?> member</span>
        </div>
        
        <div class="space-y-2.5">
            <?php $memberIndex = 0; while ($member = $result->fetch_assoc()): $memberIndex++; ?>
            <div class="glass-card hover-glow p-3.5 animate-fade-in-up" style="animation-delay: <?= 0.2 + ($memberIndex * 0.05) ?>s;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <?php if ($member['foto']): ?>
                        <div class="relative group">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Crect fill='%231a2236' width='48' height='48' rx='8'/%3E%3C/svg%3E" 
                                 data-src="serve-file.php?file=<?= urlencode($member['foto']) ?>" 
                                 alt="" 
                                 class="w-11 h-11 rounded-xl object-cover cursor-pointer transition-all duration-300 group-hover:scale-105 lazy-img"
                                 style="border: 1px solid rgba(147,129,255,0.15);"
                                 onclick="showImage(this.dataset.src)">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500 spinner hidden loader" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="32"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">@<?= htmlspecialchars($member['username']) ?></p>
                            <div class="flex items-center gap-1 text-green-400 text-xs">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <?= htmlspecialchars($member['foto']) ?>
                            </div>
                            <?php if ($member['foto_uploaded_at']): ?>
                            <p class="text-gray-600 text-xs"><?= date('d M Y, H:i', strtotime($member['foto_uploaded_at'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xs font-bold text-gray-500"
                             style="background: rgba(147,129,255,0.05); border: 1px solid rgba(147,129,255,0.08);">
                            <?= strtoupper(substr($member['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">@<?= htmlspecialchars($member['username']) ?></p>
                            <p class="text-gray-500 text-xs">Belum upload</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($member['foto']): ?>
                    <div class="flex items-center gap-3">
                        <button onclick="showImage('serve-file.php?file=<?= urlencode($member['foto']) ?>')" 
                                class="text-accent text-xs hover:underline font-medium transition-colors">Lihat</button>
                        <a href="serve-file.php?file=<?= urlencode($member['foto']) ?>&download=1" 
                           class="text-gray-400 text-xs hover:text-white font-medium transition-colors">Download</a>
                        <form method="POST" onsubmit="return confirm('Hapus dokumen @<?= htmlspecialchars($member['username']) ?>?')" class="inline">
                            <input type="hidden" name="delete_foto" value="1">
                            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                            <input type="hidden" name="foto_name" value="<?= htmlspecialchars($member['foto']) ?>">
                            <button type="submit" class="text-red-400 text-xs hover:text-red-300 font-medium transition-colors">Hapus</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Image Modal -->
        <div id="imageModal" class="image-modal fixed inset-0 bg-black/85 z-50 hidden items-center justify-center p-4" onclick="closeImage()">
            <img id="modalImage" src="" class="max-w-full max-h-full object-contain animate-scale-in">
            <button onclick="closeImage()" class="absolute top-4 right-4 w-10 h-10 rounded-xl flex items-center justify-center text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <!-- Back link -->
        <div class="mt-8 text-center animate-fade-in">
            <a href="index.php" class="inline-flex items-center gap-1.5 text-gray-500 text-sm hover:text-accent transition-all duration-300 hover:translate-x-[-3px]">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"/>
                    <path d="M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke daftar member
            </a>
        </div>
    </div>
    
    <script>
        function showImage(src) {
            var modal = document.getElementById('imageModal');
            var img = document.getElementById('modalImage');
            img.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeImage() {
            var modal = document.getElementById('imageModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeImage();
        });
        
        // Lazy loading images
        document.addEventListener('DOMContentLoaded', function() {
            var lazyImages = document.querySelectorAll('.lazy-img');
            
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        var loader = img.parentElement.querySelector('.loader');
                        
                        // Show loader
                        if (loader) loader.classList.remove('hidden');
                        
                        // Load actual image
                        img.src = img.dataset.src;
                        img.onload = function() {
                            if (loader) loader.classList.add('hidden');
                            img.classList.add('loaded');
                        };
                        
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '50px' });
            
            lazyImages.forEach(function(img) {
                observer.observe(img);
            });
        });
    </script>
</body>
</html>
