</div><!-- Main Content End -->

    <footer style="margin-left: var(--sidebar-width); padding: 20px 30px; background: white; border-top: 1px solid #e0e0e0; margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; color: #666;">
            <div>
                &copy; <?php echo date('Y'); ?> Tankmarine Ship Management. Tüm hakları saklıdır.
            </div>
            <div style="display: flex; gap: 20px; font-size: 0.9rem;">
                <span><i class="fas fa-code"></i> Version 1.0</span>
                <span><i class="fas fa-clock"></i> <?php echo date('d.m.Y H:i'); ?></span>
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($admin['username']); ?></span>
            </div>
        </div>
    </footer>

    <script>
        // Image preview function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Confirm delete
        function confirmDelete(message = 'Bu kaydı silmek istediğinizden emin misiniz?') {
            return confirm(message);
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Table search function
        function searchTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const td = tr[i].getElementsByTagName("td");
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? "" : "none";
            }
        }

        // Quick stats refresh (optional - if you want real-time updates)
        function refreshStats() {
            // This can be implemented with AJAX for real-time updates
            console.log('Stats refreshed');
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.transform = sidebar.style.transform === 'translateX(0px)' ? 'translateX(-100%)' : 'translateX(0px)';
        }

        // Notification system (if needed in the future)
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'notification notification-' + type;
            notification.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
            notification.style.cssText = `
                position: fixed;
                top: 90px;
                right: 20px;
                padding: 15px 20px;
                background: white;
                border-left: 4px solid var(--${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'accent'});
                border-radius: 5px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            document.body.appendChild(notification);
            
            setTimeout(function() {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Auto-save draft functionality (optional for content editors)
        let autoSaveTimer;
        function enableAutoSave(formId, interval = 60000) {
            clearInterval(autoSaveTimer);
            autoSaveTimer = setInterval(function() {
                const form = document.getElementById(formId);
                if (form) {
                    const formData = new FormData(form);
                    // Save to localStorage or send AJAX request
                    console.log('Auto-saving draft...');
                }
            }, interval);
        }

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('[required]');
            let valid = true;

            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--danger)';
                    valid = false;
                } else {
                    input.style.borderColor = '#ddd';
                }
            });

            return valid;
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showNotification('Panoya kopyalandı!', 'success');
            }).catch(function(err) {
                console.error('Kopyalama hatası:', err);
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save (prevent default browser save)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.querySelector('button[type="submit"]');
                if (saveBtn) {
                    saveBtn.click();
                    showNotification('Kaydediliyor...', 'info');
                }
            }
            
            // Ctrl/Cmd + K for quick search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });

        // Print functionality
        function printPage() {
            window.print();
        }

        // Export table to CSV
        function exportTableToCSV(tableId, filename = 'export.csv') {
            const table = document.getElementById(tableId);
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [];
                const cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                
                csv.push(row.join(','));
            }
            
            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', filename);
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Smooth scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Show scroll to top button when scrolled
        window.addEventListener('scroll', function() {
            const mainContent = document.querySelector('.main-content');
            if (window.pageYOffset > 300) {
                if (!document.getElementById('scrollTopBtn')) {
                    const btn = document.createElement('button');
                    btn.id = 'scrollTopBtn';
                    btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                    btn.style.cssText = `
                        position: fixed;
                        bottom: 30px;
                        right: 30px;
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        background: var(--accent);
                        color: white;
                        border: none;
                        cursor: pointer;
                        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                        z-index: 999;
                        transition: all 0.3s;
                    `;
                    btn.onclick = scrollToTop;
                    btn.onmouseenter = function() {
                        this.style.transform = 'scale(1.1)';
                        this.style.background = 'var(--secondary)';
                    };
                    btn.onmouseleave = function() {
                        this.style.transform = 'scale(1)';
                        this.style.background = 'var(--accent)';
                    };
                    document.body.appendChild(btn);
                }
            } else {
                const btn = document.getElementById('scrollTopBtn');
                if (btn) {
                    btn.remove();
                }
            }
        });

        // Initialize tooltips (if using Bootstrap or similar)
        document.addEventListener('DOMContentLoaded', function() {
            // Add any initialization code here
            console.log('Admin panel loaded successfully');
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @media print {
            .sidebar,
            .top-header,
            footer,
            .btn,
            #scrollTopBtn {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                margin-top: 0 !important;
            }
        }

        @media (max-width: 768px) {
            footer {
                margin-left: 0 !important;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            footer > div:last-child {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</body>
</html>