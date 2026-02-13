// Main JavaScript untuk aplikasi

document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token untuk AJAX requests jika diperlukan
    
    // Sidebar active link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
});

// Helper function untuk format rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

// Helper function untuk parse rupiah ke number
function parseRupiah(rupiah) {
    return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
}
