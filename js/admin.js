(() => {
    const toastRoot = document.getElementById('toastRoot');

    window.adminToast = (message, type = 'success') => {
        if (!toastRoot) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toastRoot.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.25s ease';
            setTimeout(() => toast.remove(), 260);
        }, 2500);
    };
})();
