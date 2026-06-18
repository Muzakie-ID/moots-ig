// Handle form submission and search
function initApp() {
    var form = document.getElementById('addForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var usernameInput = document.getElementById('username');
            var submitBtn = document.getElementById('submitBtn');
            var username = usernameInput.value.trim();
            
            if (!username) {
                showToast('Username tidak boleh kosong', 'error');
                return;
            }
            
            // Disable button saat submit
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menambah...';
            
            // Submit form via fetch
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
                                usernameInput.value = '';
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
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.member-item');
            var hasResults = false;
            
            items.forEach(function(item) {
                var username = item.getAttribute('data-username');
                if (username.indexOf(query) !== -1) {
                    item.style.display = 'flex';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide empty state based on results
            var emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = hasResults ? 'none' : 'block';
            }
        });
    }
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
