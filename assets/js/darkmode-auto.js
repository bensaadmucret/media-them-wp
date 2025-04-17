// Mode sombre automatique selon l'heure et l'option Customizer
(function(){
    const darkAuto = window.lejda_darkmode_auto || false;
    if(!darkAuto) return;
    const hour = new Date().getHours();
    // Nuit entre 20h (20) et 7h (7)
    const isNight = (hour >= 20 || hour < 7);
    // On ne force le dark que si aucune préférence utilisateur n'est enregistrée
    if(!localStorage.getItem('lejournaldesactus_theme')) {
        // Appelle la fonction applyTheme du script principal
        if(typeof applyTheme === 'function') {
            applyTheme(isNight ? 'dark' : 'light');
        } else if(window.jQuery && typeof jQuery === 'function') {
            // Si dans une IIFE jQuery, déclenche un event personnalisé
            document.dispatchEvent(new CustomEvent('lejdaAutoDarkMode', { detail: { theme: isNight ? 'dark' : 'light' } }));
        }
    }
})();
