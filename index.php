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
            <div class="mt-2 flex justify-center gap-2">
                <a href="login.php" class="text-xs text-gray-500 hover:text-accent transition-colors">Login</a>
                <span class="text-gray-600">|</span>
                <a href="upload.php" class="text-xs text-gray-500 hover:text-accent transition-colors">Upload Foto</a>
            </div>
        </header>
        
        <!-- Search & Add -->
        <section class="mb-4">
            <div class="flex gap-2 items-center">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Cari username..." 
                        autocomplete="off"
                        class="w-full bg-dark-secondary border border-dark-border rounded-lg pl-10 pr-4 py-3 text-white text-sm
                               placeholder-gray-500 outline-none focus:border-accent transition-colors"
                    >
                </div>
                <button 
                    id="addBtn"
                    class="flex items-center justify-center w-12 h-12 bg-accent hover:bg-accent-hover rounded-lg transition-all duration-200 active:scale-95"
                >
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
            </div>
            <!-- Member count badge -->
            <div class="mt-2 flex items-center justify-between">
                <span id="memberCount" class="text-xs text-gray-500"></span>
                <span id="searchResultText" class="text-xs text-gray-500 hidden"></span>
            </div>
        </section>
        
        <!-- List Member -->
        <section class="mb-6">
            <div id="memberList">
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $member): ?>
                        <a href="redirect.php?username=<?= urlencode($member['username']) ?>" 
                           data-username="<?= strtolower(htmlspecialchars($member['username'])) ?>"
                           class="member-item flex items-center justify-between py-3 px-4 bg-dark-secondary rounded-lg mb-2 border border-dark-border 
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
                    <div class="empty-state text-center py-12 text-gray-500">
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
            <!-- No results message -->
            <div id="noResults" class="hidden text-center py-8">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
                <p class="text-gray-500 text-sm">Tidak ada hasil untuk "<span id="searchQuery"></span>"</p>
            </div>
        </section>
        
        <!-- Footer - Pusat -->
        <footer class="mt-8 pt-6 border-t border-dark-border">
            <p class="text-center text-gray-500 text-xs mb-4">Pusat</p>
            <div class="flex justify-center gap-4">
                <!-- Trisvugm -->
                <a href="https://instagram.com/trisvugm" target="_blank" 
                   class="flex items-center gap-2 px-4 py-2 bg-dark-secondary rounded-lg border border-dark-border 
                          hover:bg-dark-border transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span class="text-white text-sm font-medium">@trisvugm</span>
                </a>
                <!-- Forkomtrisvugm -->
                <a href="https://instagram.com/forkomtrisvugm" target="_blank" 
                   class="flex items-center gap-2 px-4 py-2 bg-dark-secondary rounded-lg border border-dark-border 
                          hover:bg-dark-border transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span class="text-white text-sm font-medium">@forkomtrisvugm</span>
                </a>
            </div>
            <p class="text-center text-gray-500 text-xs mt-4">Dibuat oleh <span class="text-accent">Muzakie</span></p>
        </footer>
    </div>
    
    <!-- Modal Tambah -->
    <div id="modal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div id="modalContent" class="bg-dark-card border border-dark-border rounded-xl w-full max-w-sm p-5 relative transform scale-95 transition-transform duration-300">
            <!-- Close button -->
            <button id="closeModal" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full hover:bg-dark-border transition-colors">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            
            <h2 class="text-white font-semibold text-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
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
                    class="w-full bg-dark-secondary border border-dark-border rounded-lg px-4 py-3 text-white text-sm
                           placeholder-gray-500 mb-4 outline-none focus:border-accent transition-colors"
                >
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full btn-add py-3"
                >
                    Tambah
                </button>
            </form>
        </div>
    </div>
    
    <!-- JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
