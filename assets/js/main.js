// Handle form submission, search, modal, and pull to refresh
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
    
    // Update member count on load
    updateMemberCount();
    
    // Modal controls with animation
    if (addBtn && modal && modalContent) {
        addBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
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
    
    // Keyboard support: ESC to close, Enter to submit
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModalFn();
        }
        if (e.key === 'Enter' && modal && !modal.classList.contains('hidden') && document.activeElement === usernameInput) {
            form.dispatchEvent(new Event('submit'));
        }
    });
    
    function closeModalFn() {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(function() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
        usernameInput.value = '';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tambah';
        }
    }
    
    // Form submission
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
    
    // Real-time search
    if (searchInput) {
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
            
            // Show/hide empty state based on results
            var emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = hasResults ? 'none' : 'block';
            }
            
            // Show "no results" message
            if (noResults && searchResultText) {
                if (query.length > 0 && !hasResults) {
                    noResults.classList.remove('hidden');
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
            
            // Update member count text
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
    
    // Pull to refresh on mobile
    var touchStartY = 0;
    var touchEndY = 0;
    var pullIndicator = document.getElementById('pullIndicator');
    
    document.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) {
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        if (window.scrollY === 0 && touchStartY > 0) {
            touchEndY = e.touches[0].clientY;
            var pullDistance = touchEndY - touchStartY;
            
            if (pullDistance > 80 && !pullIndicator) {
                // Create pull indicator
                var indicator = document.createElement('div');
                indicator.id = 'pullIndicator';
                indicator.className = 'fixed top-0 left-0 right-0 z-40 bg-accent text-white text-center py-2 text-sm font-medium';
                indicator.textContent = 'Lepaskan untuk refresh...';
                document.body.appendChild(indicator);
            }
        }
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        if (window.scrollY === 0 && touchStartY > 0) {
            var pullDistance = touchEndY - touchStartY;
            
            if (pullDistance > 80) {
                // Refresh page
                location.reload();
            }
            
            touchStartY = 0;
            touchEndY = 0;
            
            var indicator = document.getElementById('pullIndicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }, { passive: true });
}

function closeModalFn() {
    var modal = document.getElementById('modal');
    var modalContent = document.getElementById('modalContent');
    var usernameInput = document.getElementById('username');
    var submitBtn = document.getElementById('submitBtn');
    
    if (modal && modalContent) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(function() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }
    if (usernameInput) usernameInput.value = '';
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Tambah';
    }
}

function showToast(message, type) {
    var oldToast = document.querySelector('.toast');
    if (oldToast) oldToast.remove();
    
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            toast.remove();
        }, 400);
    }, 3000);
}

// Run on DOM ready or immediately if already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

function showToast(message, type) {
    // Hapus toast lama kalau ada
    var oldToast = document.querySelector('.toast');
    if (oldToast) oldToast.remove();
    
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    // Hide after 3s
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            toast.remove();
        }, 400);
    }, 3000);
}
