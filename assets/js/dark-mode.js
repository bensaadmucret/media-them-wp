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
        initDarkModeSwitch();
    });
    
    /**
     * Initialiser le switch Bootstrap pour le mode sombre
     */
    function initDarkModeSwitch() {
        // Récupérer l'état actuel du thème
        var currentTheme = document.documentElement.getAttribute('data-theme');
        
        // Récupérer le switch
        var darkModeSwitch = document.getElementById('darkModeSwitch');
        
        if (darkModeSwitch) {
            // Définir l'état initial du switch
            darkModeSwitch.checked = currentTheme === 'dark';
            
            // Ajouter l'événement change
            darkModeSwitch.addEventListener('change', function() {
                // Définir le nouveau thème
                var newTheme = this.checked ? 'dark' : 'light';
                
                // Appliquer le thème
                document.documentElement.setAttribute('data-theme', newTheme);
                
                // Sauvegarder la préférence
                localStorage.setItem('lejournaldesactus_theme', newTheme);
                
                // Si l'utilisateur est connecté, sauvegarder dans la base de données
                if (typeof lejournaldesactusDarkMode !== 'undefined') {
                    $.ajax({
                        url: lejournaldesactusDarkMode.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'lejournaldesactus_save_theme_preference',
                            theme: newTheme,
                            nonce: lejournaldesactusDarkMode.nonce
                        }
                    });
                }
            });
        }
        
        // Bouton flottant dans le footer
        var toggleBtn = document.querySelector('.dark-mode-toggle-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Basculer le thème
                var newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                
                // Appliquer le thème
                document.documentElement.setAttribute('data-theme', newTheme);
                
                // Mettre à jour l'état du switch
                if (darkModeSwitch) {
                    darkModeSwitch.checked = newTheme === 'dark';
                }
                
                // Sauvegarder la préférence
                localStorage.setItem('lejournaldesactus_theme', newTheme);
                
                // Si l'utilisateur est connecté, sauvegarder dans la base de données
                if (typeof lejournaldesactusDarkMode !== 'undefined') {
                    $.ajax({
                        url: lejournaldesactusDarkMode.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'lejournaldesactus_save_theme_preference',
                            theme: newTheme,
                            nonce: lejournaldesactusDarkMode.nonce
                        }
                    });
                }
            });
        }
    }
    
})(jQuery);
