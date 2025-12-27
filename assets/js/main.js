document.addEventListener('DOMContentLoaded', () => {
    // --- Toast Notification System ---
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    document.body.appendChild(toastContainer);

    window.showToast = (message, type = 'info') => {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        // Map 'danger' to 'error' style if needed
        if (type === 'danger') { icon = 'exclamation-triangle'; toast.classList.add('error'); }

        toast.innerHTML = `
            <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
            <span>${message}</span>
        `;

        toastContainer.appendChild(toast);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // Check for PHP Flash Messages
    const flashEl = document.getElementById('flash-message');
    if (flashEl) {
        const msg = flashEl.getAttribute('data-message');
        const type = flashEl.getAttribute('data-type');
        if (msg) showToast(msg, type);
    }

    // --- Mobile Sidebar Toggle ---
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');

    if (menuBtn && sidebar) {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // --- Table Search ---
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search records...';
    searchInput.className = 'form-control mb-3';
    searchInput.style.width = '100%';
    searchInput.style.padding = '0.75rem';
    searchInput.style.marginBottom = '1rem';
    searchInput.style.borderRadius = '8px';
    searchInput.style.border = '1px solid #e2e8f0';

    const tables = document.querySelectorAll('table');
    if (tables.length > 0) {
        tables.forEach(table => {
            const container = table.closest('.table-responsive') || table.parentElement;
            // Only add if not already present
            if (!container.querySelector('input[type="text"]')) {
                const searchClone = searchInput.cloneNode(true);
                container.insertBefore(searchClone, table.closest('.table-responsive') || table);

                searchClone.addEventListener('keyup', (e) => {
                    const term = e.target.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            }
        });
    }
});
