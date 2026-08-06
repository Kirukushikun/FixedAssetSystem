if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/service-worker.js').catch(function (err) {
            console.error('Service worker registration failed:', err);
        });
    });
}
