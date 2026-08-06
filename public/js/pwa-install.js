(function () {
    var deferredPrompt = null;
    var installBtn = document.getElementById('pwa-install-btn');
    var iosBanner = document.getElementById('pwa-ios-banner');

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;
    }

    function isIosSafari() {
        var ua = window.navigator.userAgent;
        var isIos = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
        var isSafari = /Safari/.test(ua) && !/CriOS|FxiOS|EdgiOS|OPiOS/.test(ua);
        return isIos && isSafari;
    }

    if (isStandalone()) {
        // Already installed — never show install UI.
        return;
    }

    if (isIosSafari() && iosBanner) {
        iosBanner.hidden = false;
    }

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredPrompt = event;
        if (installBtn) {
            installBtn.hidden = false;
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', function () {
            if (!deferredPrompt) {
                return;
            }
            installBtn.hidden = true;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.finally(function () {
                deferredPrompt = null;
            });
        });
    }

    window.addEventListener('appinstalled', function () {
        deferredPrompt = null;
        if (installBtn) {
            installBtn.hidden = true;
        }
        if (iosBanner) {
            iosBanner.hidden = true;
        }
    });
})();
