/**
 * Script simple pour gérer le switch Bootstrap de mode sombre
 */
jQuery(document).ready(function($) {
    
    // Récupérer le switch
    var darkModeSwitch = document.getElementById('darkModeSwitch');
    
    if (!darkModeSwitch) {
        console.error('Switch de mode sombre non trouvé dans le DOM');
        return;
    }
    
    // Définir l'état initial du switch en fonction du thème actuel
    var currentTheme = document.documentElement.getAttribute('data-theme');
    darkModeSwitch.checked = currentTheme === 'dark';
    
    // Ajouter l'événement change
    $(darkModeSwitch).on('change', function() {
        var newTheme = this.checked ? 'dark' : 'light';
        console.log('Switch changé! Nouveau thème:', newTheme);
        
        // Appliquer le thème
        document.documentElement.setAttribute('data-theme', newTheme);
        
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
