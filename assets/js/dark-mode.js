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
        
        // Ajouter/supprimer la classe dark-mode sur le body
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        
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
                    
                    // Ajouter/supprimer la classe dark-mode sur le body
                    if (newTheme === 'dark') {
                        document.body.classList.add('dark-mode');
                    } else {
                        document.body.classList.remove('dark-mode');
                    }
                }
            });
        }
        
        // Ajouter un écouteur d'événement pour le bouton de basculement
        $('.dark-mode-menu-toggle, .dark-mode-toggle').on('click', function(e) {
            e.preventDefault();
            
            // Basculer entre les modes
            var newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            
            // Appliquer le thème
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Ajouter/supprimer la classe dark-mode sur le body
            if (newTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
            
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
