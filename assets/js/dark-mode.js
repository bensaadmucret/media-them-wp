(function($) {
    'use strict';
    
    // Variables
    var currentTheme = 'light';
    
    // Fonction pour appliquer le thème
    function applyTheme(theme) {
        // Appliquer le thème à l'élément HTML
        document.documentElement.setAttribute('data-theme', theme);
        
        // Ajouter/supprimer la classe dark-mode sur le body
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        
        // Mettre à jour le switch Bootstrap si présent
        var darkModeSwitch = document.getElementById('darkModeSwitch');
        if (darkModeSwitch) {
            darkModeSwitch.checked = theme === 'dark';
        }
        
        // Déclencher un événement personnalisé pour informer les autres scripts
        var event = new CustomEvent('themeChanged', { detail: { theme: theme } });
        document.dispatchEvent(event);
        
        currentTheme = theme;
    }
    
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
        applyTheme(savedTheme);
    })();
    
    // Initialisation
    $(document).ready(function() {
        // Écouter les changements de préférence du système
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('lejournaldesactus_theme')) {
                    var newTheme = e.matches ? 'dark' : 'light';
                    applyTheme(newTheme);
                }
            });
        }
        
        // Ajouter un écouteur d'événement pour le bouton de basculement
        $('.dark-mode-menu-toggle, .dark-mode-toggle').on('click', function(e) {
            e.preventDefault();
            
            // Basculer entre les modes
            var newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            
            // Appliquer le thème
            applyTheme(newTheme);
            
            // Sauvegarder la préférence
            localStorage.setItem('lejournaldesactus_theme', newTheme);
            
            // Sauvegarder dans la base de données via AJAX
            $.ajax({
                url: lejournaldesactusDarkMode.ajaxurl,
                type: 'POST',
                data: {
                    action: 'lejournaldesactus_save_theme_preference',
                    theme: newTheme,
                    nonce: lejournaldesactusDarkMode.nonce
                }
            });
        });
    });
    
})(jQuery);
