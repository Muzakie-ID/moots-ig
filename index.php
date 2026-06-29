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
    <meta name="theme-color" content="#0a0a14">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="Daftar member Instagram komunitas MOTS - Kunjungi profile IG member kami">
    <title>Mots IG - Daftar Member</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            primary: '#0a0a14',
                            secondary: '#10101e',
                            card: '#161628',
                            border: '#1e1c36',
                        },
                        accent: '#9381ff',
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
<body class="bg-dark-primary min-h-screen font-inter">
    
    <!-- Animated Background -->
    <div class="bg-scene" aria-hidden="true">
        <div class="bg-orb bg-orb--1"></div>
        <div class="bg-orb bg-orb--2"></div>
        <div class="bg-orb bg-orb--3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>
    
    <!-- Main Content -->
    <div class="content-wrapper w-full max-w-md mx-auto px-4 py-8">
        <!-- Header -->
        <header class="text-center mb-8 animate-fade-in">
            <div class="mb-3">
                <h1 class="text-3xl font-extrabold gradient-text tracking-tight">Mots IG</h1>
            </div>
            <p class="text-gray-500 text-sm font-light tracking-wide">Kunjungi profile IG member kami</p>
            <div class="mt-3 flex justify-center gap-3">
                <a href="login.php" class="text-xs text-gray-500 hover:text-accent transition-all duration-300 hover:translate-y-[-1px]">Login</a>
                <span class="text-gray-700">•</span>
                <a href="upload.php" class="text-xs text-gray-500 hover:text-accent transition-all duration-300 hover:translate-y-[-1px]">Upload Foto</a>
            </div>
        </header>
        
        <!-- Search & Add -->
        <section class="mb-5 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 transition-colors duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Cari username..." 
                    autocomplete="off"
                    class="search-glass w-full pl-10 pr-4 py-3 text-white text-sm
                           placeholder-gray-500 outline-none"
                >
            </div>
            <!-- Member count badge -->
            <div class="mt-2.5 flex items-center justify-between">
                <span id="memberCount" class="text-xs text-gray-500 font-medium"></span>
                <span id="searchResultText" class="text-xs text-gray-500 hidden"></span>
            </div>
        </section>
        
        <!-- List Member -->
        <section class="mb-6">
            <div id="memberList">
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $index => $member): ?>
                        <a href="redirect.php?username=<?= urlencode($member['username']) ?>" 
                           data-username="<?= strtolower(htmlspecialchars($member['username'])) ?>"
                           class="member-item flex items-center justify-between py-3.5 px-4 mb-2.5
                                  animate-in stagger-<?= min($index + 1, 15) ?>"
                           style="text-decoration: none;">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                                     style="background: linear-gradient(135deg, #9381ff, #b8b8ff);">
                                    <?= strtoupper(substr($member['username'], 0, 1)) ?>
                                </div>
                                <span class="text-white font-medium text-sm">@<?= htmlspecialchars($member['username']) ?></span>
                            </div>
                            <span class="visit-arrow flex items-center gap-1.5 text-accent text-xs font-medium">
                                Kunjungi
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M7 17L17 7"/>
                                    <path d="M7 7h10v10"/>
                                </svg>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state text-center py-16 text-gray-500 animate-fade-in">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: rgba(147,129,255,0.08);">
                            <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Belum ada member</p>
                        <p class="text-xs text-gray-600 mt-1">Klik tombol + untuk menambahkan</p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- No results message -->
            <div id="noResults" class="hidden text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: rgba(147,129,255,0.08);">
                    <svg class="w-8 h-8 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">Tidak ada hasil untuk "<span id="searchQuery" class="text-accent"></span>"</p>
            </div>
        </section>
        
        <!-- Footer - Pusat -->
        <footer class="mt-10 pt-6 border-t border-dark-border animate-fade-in" style="animation-delay: 0.5s;">
            <p class="text-center text-gray-600 text-xs mb-4 font-medium uppercase tracking-widest">Pusat</p>
            <div class="flex justify-center gap-3">
                <!-- Trisvugm -->
                <a href="https://instagram.com/trisvugm" target="_blank" rel="noopener noreferrer"
                   class="ig-link-card flex items-center gap-2.5 px-4 py-2.5">
                    <svg class="w-4 h-4 text-pink-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span class="text-white text-sm font-medium">@trisvugm</span>
                </a>
                <!-- Forkomtrisvugm -->
                <a href="https://instagram.com/forkomtrisvugm" target="_blank" rel="noopener noreferrer"
                   class="ig-link-card flex items-center gap-2.5 px-4 py-2.5">
                    <svg class="w-4 h-4 text-purple-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span class="text-white text-sm font-medium">@forkomtrisvugm</span>
                </a>
            </div>
            <p class="text-center text-gray-600 text-xs mt-5">Dibuat oleh <span class="gradient-text font-semibold">Muzakie</span></p>
        </footer>
    </div>
    
    <!-- Modal Tambah -->
    <div id="modal" class="modal-backdrop fixed inset-0 z-50 hidden items-center justify-center p-4 opacity-0 transition-all duration-400">
        <div id="modalContent" class="modal-content rounded-2xl w-full max-w-sm p-6 relative transform scale-95 transition-all duration-400">
            <!-- Close button -->
            <button id="closeModal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/5 transition-colors">
                <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            
            <h2 class="text-white font-semibold text-lg mb-5 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #9381ff, #b8b8ff);">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                Tambah Member
            </h2>
            
            <form id="addForm">
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Username Instagram" 
                    autocomplete="off"
                    required
                    class="w-full px-4 py-3 text-white text-sm
                           placeholder-gray-500 mb-4 outline-none"
                >
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full btn-gradient py-3 rounded-xl text-sm"
                >
                    Tambah
                </button>
            </form>
        </div>
    </div>
    
    <!-- Floating Action Button -->
    <button 
        id="addBtn"
        class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 btn-gradient rounded-full shadow-lg neon-glow"
        aria-label="Tambah member"
        style="box-shadow: 0 4px 25px rgba(147, 129, 255, 0.4);">
        <i class="fa fa-plus text-white text-lg" aria-hidden="true"></i>
    </button>
    
    <!-- JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
