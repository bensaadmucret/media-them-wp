/**
 * Script pour gérer le mode sombre/clair
 */
(function($) {
    'use strict';
    
    // Variables
    var notificationTimeout;
    var currentTheme = 'light';
    
    // Exécution immédiate pour appliquer le thème avant tout rendu
    (function() {
        var savedTheme = localStorage.getItem('lejournaldesactus_theme');
        var appliedTheme = savedTheme;
        
        if (!appliedTheme) {
            // Vérifier les préférences du système
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                appliedTheme = 'dark';
            } else {
                appliedTheme = 'light';
            }
        }
        
        // Si le thème est "auto", utiliser les préférences du système
        if (appliedTheme === 'auto') {
            appliedTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Appliquer le thème immédiatement
        document.documentElement.setAttribute('data-theme', appliedTheme);
    })();
    
    // Initialisation
    $(document).ready(function() {
        initTheme();
        initToggleButton();
        initMenuToggle();
    });
    
    /**
     * Initialiser le thème
     */
    function initTheme() {
        // Vérifier si l'utilisateur a déjà une préférence
        var savedTheme = localStorage.getItem('lejournaldesactus_theme');
        
        if (savedTheme) {
            // Utiliser la préférence sauvegardée
            setTheme(savedTheme);
        } else {
            // Vérifier les préférences du système
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                setTheme('dark');
            } else {
                setTheme('light');
            }
            
            // Écouter les changements de préférence du système
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem('lejournaldesactus_theme') || localStorage.getItem('lejournaldesactus_theme') === 'auto') {
                    setTheme(e.matches ? 'dark' : 'light', false);
                }
            });
        }
    }
    
    /**
     * Initialiser le bouton de basculement
     */
    function initToggleButton() {
        // Le bouton flottant a été supprimé, cette fonction est maintenue pour compatibilité
        // mais n'ajoute plus d'événements sur le bouton flottant
    }
    
    /**
     * Initialiser le basculement dans le menu
     */
    function initMenuToggle() {
        $('.dark-mode-menu-toggle').on('click', function(e) {
            e.preventDefault();
            
            // Basculer entre les modes
            var newTheme = $('html').attr('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(newTheme, true);
            saveThemePreference(newTheme);
        });
    }
    
    /**
     * Définir le thème
     */
    function setTheme(theme, showNotification) {
        var appliedTheme = theme;
        
        // Si le thème est "auto", utiliser les préférences du système
        if (theme === 'auto') {
            appliedTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        // Appliquer le thème
        $('html').attr('data-theme', appliedTheme);
        document.documentElement.setAttribute('data-theme', appliedTheme); // Assurer la compatibilité avec tous les navigateurs
        currentTheme = theme;
        
        // Forcer le rafraîchissement des styles
        setTimeout(function() {
            document.body.style.display = 'none';
            document.body.offsetHeight; // Forcer un reflow
            document.body.style.display = '';
        }, 10);
        
        // Mettre à jour les boutons
        updateThemeButtons(theme);
        
        // Afficher une notification
        if (showNotification) {
            var message = '';
            
            if (theme === 'dark') {
                message = lejournaldesactusDarkMode.darkModeText + ' ' + __('activé', 'lejournaldesactus');
            } else if (theme === 'light') {
                message = lejournaldesactusDarkMode.lightModeText + ' ' + __('activé', 'lejournaldesactus');
            } else {
                message = lejournaldesactusDarkMode.autoModeText + ' ' + __('activé', 'lejournaldesactus');
            }
            
            showThemeNotification(message);
        }
    }
    
    /**
     * Mettre à jour les boutons de thème
     */
    function updateThemeButtons(theme) {
        // Mettre à jour le menu déroulant
        $('.theme-dropdown-item').removeClass('active');
        $('.theme-dropdown-item[data-theme="' + theme + '"]').addClass('active');
        
        // Mettre à jour le texte du menu
        var menuText = '';
        
        if (theme === 'dark') {
            menuText = lejournaldesactusDarkMode.darkModeText;
        } else if (theme === 'light') {
            menuText = lejournaldesactusDarkMode.lightModeText;
        } else {
            menuText = lejournaldesactusDarkMode.autoModeText;
        }
        
        $('.dark-mode-text').text(menuText);
    }
    
    /**
     * Sauvegarder la préférence de thème
     */
    function saveThemePreference(theme) {
        // Sauvegarder dans le localStorage
        localStorage.setItem('lejournaldesactus_theme', theme);
        
        // Si l'utilisateur est connecté, sauvegarder dans la base de données
        if (lejournaldesactusDarkMode.loggedIn) {
            $.ajax({
                url: lejournaldesactusDarkMode.ajaxurl,
                type: 'POST',
                data: {
                    action: 'lejournaldesactus_save_theme_preference',
                    theme: theme,
                    nonce: lejournaldesactusDarkMode.nonce
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Erreur lors de la sauvegarde de la préférence de thème:', response.data);
                    }
                },
                error: function() {
                    console.error('Erreur lors de la sauvegarde de la préférence de thème');
                }
            });
        }
    }
    
    /**
     * Afficher une notification de changement de thème
     */
    function showThemeNotification(message) {
        // Supprimer les notifications existantes
        $('.theme-notification').remove();
        clearTimeout(notificationTimeout);
        
        // Créer la notification
        var $notification = $('<div class="theme-notification">' + message + '</div>');
        $('body').append($notification);
        
        // Afficher la notification
        setTimeout(function() {
            $notification.addClass('show');
        }, 10);
        
        // Masquer la notification après 3 secondes
        notificationTimeout = setTimeout(function() {
            $notification.removeClass('show');
            
            // Supprimer la notification après l'animation
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
    /**
     * Fonction de traduction simple
     */
    function __(text, domain) {
        return text;
    }
    
})(jQuery);
