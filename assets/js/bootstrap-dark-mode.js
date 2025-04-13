/**
 * Script simple pour gérer le switch Bootstrap de mode sombre
 */
jQuery(document).ready(function($) {
    
    // Récupérer le switch
    var darkModeSwitch = document.getElementById('darkModeSwitch');
    
    if (!darkModeSwitch) {
        console.log('Switch de mode sombre non trouvé dans le DOM');
        return;
    }
    
    // Définir l'état initial du switch en fonction du thème actuel
    var currentTheme = document.documentElement.getAttribute('data-theme');
    darkModeSwitch.checked = currentTheme === 'dark';
    
    // Écouter les changements de thème pour mettre à jour le switch
    document.addEventListener('themeChanged', function(e) {
        if (darkModeSwitch) {
            darkModeSwitch.checked = e.detail.theme === 'dark';
        }
    });
    
    // Ajouter l'événement change
    $(darkModeSwitch).on('change', function() {
        var newTheme = this.checked ? 'dark' : 'light';
        console.log('Switch changé! Nouveau thème:', newTheme);
        
        // Appliquer le thème via la fonction globale si disponible
        if (typeof applyTheme === 'function') {
            applyTheme(newTheme);
        } else {
            // Fallback si la fonction n'est pas disponible
            document.documentElement.setAttribute('data-theme', newTheme);
            if (newTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
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
