/**
 * Gestion du formulaire de newsletter avec JavaScript moderne
 */
// Script de gestion du formulaire de newsletter
'use strict';

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form.cta-form, form.newsletter-form');
    
    forms.forEach(form => {
        // Forcer l'utilisation de la méthode directe pour tous les formulaires
        const formAction = form.getAttribute('action');
        
        // Si le formulaire n'a pas d'action, lui en ajouter une
        if (!formAction) {
            form.setAttribute('action', lejournaldesactus_newsletter.direct_url);
            form.setAttribute('method', 'post');
        }
        
        form.addEventListener('submit', function(e) {
            // Ne pas empêcher la soumission normale du formulaire
            // Stocker les données du formulaire pour les utiliser après la redirection
            const formData = {
                name: form.querySelector('[name="name"]').value,
                email: form.querySelector('[name="email"]').value
            };
            
            // Stocker les données dans le localStorage pour les récupérer après la redirection
            localStorage.setItem('newsletter_submission', JSON.stringify(formData));
            
            // Laisser le formulaire se soumettre normalement
            return true;
        });
    });
    
    // Vérifier si nous venons de soumettre un formulaire (approche directe)
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('newsletter_status');
    const message = urlParams.get('message');
    
    if (status) {
        // Récupérer les données du formulaire
        const formData = JSON.parse(localStorage.getItem('newsletter_submission') || '{}');
        
        // Afficher le message approprié
        const messageContainer = document.querySelector('.newsletter-message');
        if (messageContainer) {
            // Utiliser le message spécifique s'il est disponible, sinon utiliser le message par défaut
            const displayMessage = message 
                ? decodeURIComponent(message) 
                : (status === 'success' 
                    ? lejournaldesactus_newsletter.success 
                    : lejournaldesactus_newsletter.error);
            
            showMessage(messageContainer, displayMessage, status);
            
            // Faire défiler jusqu'au message si nécessaire
            if (!isElementInViewport(messageContainer)) {
                messageContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        // Nettoyer le localStorage
        localStorage.removeItem('newsletter_submission');
        
        // Nettoyer l'URL pour éviter de réafficher le message lors d'un rafraîchissement
        if (window.history && window.history.replaceState) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }
});

/**
 * Affiche un message dans le conteneur spécifié
 * @param {HTMLElement} container - Le conteneur où afficher le message
 * @param {string} message - Le message à afficher
 * @param {string} type - Le type de message ('success' ou 'error')
 */
function showMessage(container, message, type) {
    container.innerHTML = '';
    container.classList.remove('success', 'error');
    
    const messageElement = document.createElement('div');
    messageElement.className = type;
    messageElement.textContent = message;
    
    container.appendChild(messageElement);
    container.classList.add(type);
    
    // Masquer le message après un délai (uniquement pour les succès)
    if (type === 'success') {
        setTimeout(() => {
            container.style.opacity = '0';
            setTimeout(() => {
                container.innerHTML = '';
                container.classList.remove('success');
                container.style.opacity = '1';
            }, 500);
        }, 5000);
    }
}

/**
 * Vérifie si un élément est visible dans la fenêtre
 * @param {HTMLElement} el - L'élément à vérifier
 * @returns {boolean} - True si l'élément est visible
 */
function isElementInViewport(el) {
    if (!el) return false;
    
    const rect = el.getBoundingClientRect();
    
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}
