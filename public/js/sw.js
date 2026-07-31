// LabAntik Kasir - Service Worker untuk notifikasi
// Diperlukan agar notifikasi bisa tampil di tray notifikasi HP (Android Chrome
// melarang new Notification() dari halaman, wajib lewat registration.showNotification()).

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Saat notifikasi di tray diklik: fokuskan tab aplikasi yang sudah terbuka,
// atau buka tab baru jika tidak ada.
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
