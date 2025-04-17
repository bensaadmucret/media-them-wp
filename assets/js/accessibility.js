// JS Accessibilité : boutons + / - pour la taille de police
(function() {
    // Valeurs limites
    var minFont = 14;
    var maxFont = 24;
    var defaultFont = document.body.style.fontSize ? parseInt(document.body.style.fontSize) : 18;
    // Charger la préférence si elle existe
    var storedFont = localStorage.getItem('lejda_fontsize');
    if (storedFont) {
        document.body.style.fontSize = storedFont + 'px';
    }

    // Augmenter la taille
    var incBtn = document.getElementById('increase-fontsize');
    var decBtn = document.getElementById('decrease-fontsize');
    function setFontSize(size) {
        if (size < minFont) size = minFont;
        if (size > maxFont) size = maxFont;
        document.body.style.fontSize = size + 'px';
        localStorage.setItem('lejda_fontsize', size);
    }
    if (incBtn) {
        incBtn.addEventListener('click', function() {
            var current = parseInt(window.getComputedStyle(document.body).fontSize);
            setFontSize(current + 1);
        });
    }
    if (decBtn) {
        decBtn.addEventListener('click', function() {
            var current = parseInt(window.getComputedStyle(document.body).fontSize);
            setFontSize(current - 1);
        });
    }
})();
