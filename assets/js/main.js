// ============================================
// MOTS IG — Enhanced Animations & Interactions
// ============================================

function initApp() {
    var modal = document.getElementById('modal');
    var modalContent = document.getElementById('modalContent');
    var addBtn = document.getElementById('addBtn');
    var closeModal = document.getElementById('closeModal');
    var form = document.getElementById('addForm');
    var usernameInput = document.getElementById('username');
    var submitBtn = document.getElementById('submitBtn');
    var searchInput = document.getElementById('searchInput');
    var memberCount = document.getElementById('memberCount');
    var noResults = document.getElementById('noResults');
    var searchResultText = document.getElementById('searchResultText');
    
    // Initialize
    updateMemberCount();
    initScrollAnimations();
    initParallaxOrbs();
    initRippleButtons();
    
    // ---- Modal Controls ----
    if (addBtn && modal && modalContent) {
        addBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.top = '-' + window.scrollY + 'px';
            setTimeout(function() {
                modal.classList.add('opacity-100');
                modal.classList.remove('opacity-0');
                modalContent.classList.add('scale-100');
                modalContent.classList.remove('scale-95');
            }, 10);
            usernameInput.focus();
        });
    }
    
    if (closeModal && modal && modalContent) {
        closeModal.addEventListener('click', function() {
            closeModalFn();
        });
    }
    
    // Close modal on backdrop click
    if (modal && modalContent) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModalFn();
            }
        });
    }
    
    // Keyboard: ESC to close, Enter to submit
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModalFn();
        }
        if (e.key === 'Enter' && modal && !modal.classList.contains('hidden') && document.activeElement === usernameInput) {
            form.dispatchEvent(new Event('submit'));
        }
    });
    
    function closeModalFn() {
        var scrollY = document.body.style.top;
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.top = '';
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY.replace('-', '')) || 0);
        }
        setTimeout(function() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 400);
        usernameInput.value = '';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tambah';
        }
    }
    
    // ---- Form Submission ----
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var username = usernameInput.value.trim();
            
            if (!username) {
                showToast('Username tidak boleh kosong', 'error');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menambah...';
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                showToast('Username berhasil ditambahkan!', 'success');
                                closeModalFn();
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                showToast(data.message || 'Gagal menambahkan', 'error');
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Tambah';
                            }
                        } catch (e) {
                            showToast('Terjadi kesalahan', 'error');
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Tambah';
                        }
                    } else {
                        showToast('Terjadi kesalahan koneksi', 'error');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Tambah';
                    }
                }
            };
            
            xhr.send('username=' + encodeURIComponent(username));
        });
    }
    
    // ---- Real-time Search with animation ----
    if (searchInput) {
        var searchIcon = searchInput.parentElement.querySelector('svg');
        
        searchInput.addEventListener('focus', function() {
            if (searchIcon) searchIcon.style.color = '#9381ff';
        });
        
        searchInput.addEventListener('blur', function() {
            if (searchIcon) searchIcon.style.color = '';
        });
        
        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.member-item');
            var hasResults = false;
            var visibleCount = 0;
            
            items.forEach(function(item) {
                var username = item.getAttribute('data-username');
                if (username.indexOf(query) !== -1) {
                    item.style.display = 'flex';
                    hasResults = true;
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide empty state
            var emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = hasResults ? 'none' : 'block';
            }
            
            // Show "no results"
            if (noResults && searchResultText) {
                if (query.length > 0 && !hasResults) {
                    noResults.classList.remove('hidden');
                    var searchQuery = document.getElementById('searchQuery');
                    if (searchQuery) searchQuery.textContent = query;
                    searchResultText.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    if (query.length > 0 && hasResults) {
                        searchResultText.classList.remove('hidden');
                        searchResultText.textContent = visibleCount + ' ditemukan';
                    } else {
                        searchResultText.classList.add('hidden');
                    }
                }
            }
            
            updateMemberCount();
        });
    }
    
    function updateMemberCount() {
        var items = document.querySelectorAll('.member-item');
        var visibleCount = 0;
        var totalCount = items.length;
        var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        
        items.forEach(function(item) {
            if (item.style.display !== 'none') {
                visibleCount++;
            }
        });
        
        if (memberCount) {
            if (query.length > 0) {
                memberCount.textContent = visibleCount + ' dari ' + totalCount + ' member';
            } else {
                memberCount.textContent = totalCount + ' member';
            }
        }
    }
    
    // ---- Pull to Refresh (mobile) ----
    var touchStartY = 0;
    var touchEndY = 0;
    
    document.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) {
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        if (window.scrollY === 0 && touchStartY > 0) {
            touchEndY = e.touches[0].clientY;
            var pullDistance = touchEndY - touchStartY;
            
            if (pullDistance > 80 && !document.getElementById('pullIndicator')) {
                var indicator = document.createElement('div');
                indicator.id = 'pullIndicator';
                indicator.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:40;text-align:center;padding:0.625rem;font-size:0.75rem;font-weight:600;color:white;background:linear-gradient(90deg,#9381ff,#b8b8ff);';
                indicator.textContent = 'Lepaskan untuk refresh...';
                document.body.appendChild(indicator);
            }
        }
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        if (window.scrollY === 0 && touchStartY > 0) {
            var pullDistance = touchEndY - touchStartY;
            
            if (pullDistance > 80) {
                location.reload();
            }
            
            touchStartY = 0;
            touchEndY = 0;
            
            var indicator = document.getElementById('pullIndicator');
            if (indicator) indicator.remove();
        }
    }, { passive: true });
}

// ============================================
// Scroll-triggered animations (IntersectionObserver)
// ============================================
function initScrollAnimations() {
    var revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length === 0) return;
    
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });
    
    revealElements.forEach(function(el) {
        observer.observe(el);
    });
}

// ============================================
// Parallax effect on background orbs (mouse)
// ============================================
function initParallaxOrbs() {
    var orbs = document.querySelectorAll('.bg-orb');
    if (orbs.length === 0 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    
    var rafId = null;
    var mouseX = 0;
    var mouseY = 0;
    
    document.addEventListener('mousemove', function(e) {
        mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        
        if (!rafId) {
            rafId = requestAnimationFrame(function() {
                orbs.forEach(function(orb, i) {
                    var depth = (i + 1) * 8;
                    var x = mouseX * depth;
                    var y = mouseY * depth;
                    orb.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
                });
                rafId = null;
            });
        }
    });
}

// ============================================
// Ripple effect on gradient buttons
// ============================================
function initRippleButtons() {
    var buttons = document.querySelectorAll('.btn-gradient, .btn-add');
    
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var rect = btn.getBoundingClientRect();
            var size = Math.max(rect.width, rect.height);
            var x = e.clientX - rect.left - size / 2;
            var y = e.clientY - rect.top - size / 2;
            
            var ripple = document.createElement('span');
            ripple.className = 'ripple-effect';
            ripple.style.cssText = 'position:absolute;border-radius:50%;background:rgba(255,255,255,0.3);pointer-events:none;width:' + size + 'px;height:' + size + 'px;left:' + x + 'px;top:' + y + 'px;animation:ripple 0.6s linear forwards;';
            
            btn.appendChild(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    });
}

// ============================================
// Toast Notification
// ============================================
function showToast(message, type) {
    // Remove existing toast
    var oldToast = document.querySelector('.toast');
    if (oldToast) oldToast.remove();
    
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Trigger entrance animation
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    // Auto-dismiss after 3s
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            toast.remove();
        }, 400);
    }, 3000);
}

// ============================================
// Run on DOM ready
// ============================================
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
