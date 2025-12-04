// Toast Notification System
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || (() => {
        const div = document.createElement('div');
        div.id = 'toast-container';
        div.className = 'toast-container';
        document.body.appendChild(div);
        return div;
    })();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    toast.style.animation = 'slideIn 0.3s ease forwards';
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// Listen for Livewire showToastr events
window.addEventListener('showToastr', function(event) {
    const data = event.detail[0];
    showToast(data.message, data.type);
});
