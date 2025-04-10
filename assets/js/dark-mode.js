(function($) {
    'use strict';
    
    // Variables
    var currentTheme = 'light';
    
    // Exécution immédiate pour appliquer le thème avant tout rendu
    (function() {
        var savedTheme = localStorage.getItem('lejournaldesactus_theme');
        
        if (!savedTheme) {
            // Vérifier les préférences du système
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                savedTheme = 'dark';
            } else {
                savedTheme = 'light';
            }
        }
        
        // Appliquer le thème immédiatement
        document.documentElement.setAttribute('data-theme', savedTheme);
        currentTheme = savedTheme;
    })();
    
    // Initialisation
    $(document).ready(function() {
        // Écouter les changements de préférence du système
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('lejournaldesactus_theme')) {
                    var newTheme = e.matches ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', newTheme);
                }
            });
        }
    });
    
})(jQuery);
