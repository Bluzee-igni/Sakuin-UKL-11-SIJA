// public/js/login.js

document.addEventListener('DOMContentLoaded', () => {
    const splashScreen = document.getElementById('splash-screen');
    const mainContent = document.getElementById('main-auth-content');

    if (splashScreen && mainContent) {
        // Tampilkan splash screen seketika
        splashScreen.style.display = 'flex';
        
        // Tunggu 1.5 detik agar animasi splash selesai
        setTimeout(() => {
            // Tambahkan class fade-out untuk transisi
            splashScreen.classList.add('fade-out');
            
            // Cegah interaksi di area splash yang sedang memudar
            splashScreen.style.pointerEvents = 'none';
            
            // Hapus element sepenuhnya dari DOM setelah fade-out selesai (500ms)
            setTimeout(() => {
                splashScreen.remove();
            }, 500);

        }, 1500);
    }
});
