/**
 * Gestion des fonctionnalités RGPD avec JavaScript moderne
 */
'use strict';

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', () => {
    initCookieBanner();
    initDataRequestForm();
});

/**
 * Initialise la bannière de cookies
 */
function initCookieBanner() {
    const cookieBanner = document.getElementById('cookie-banner');
    if (!cookieBanner) return;
    
    // Vérifier si le cookie d'acceptation existe déjà
    if (getCookie(lejournaldesactus_rgpd.cookie_name)) {
        cookieBanner.style.display = 'none';
        return;
    }
    
    // Afficher la bannière
    cookieBanner.classList.add('active');
    
    // Gérer le clic sur le bouton d'acceptation
    const acceptButton = document.getElementById('accept-cookies');
    if (acceptButton) {
        acceptButton.addEventListener('click', () => {
            // Définir le cookie d'acceptation
            setCookie(
                lejournaldesactus_rgpd.cookie_name,
                'true',
                lejournaldesactus_rgpd.cookie_expiry
            );
            
            // Masquer la bannière avec animation
            cookieBanner.classList.remove('active');
            setTimeout(() => {
                cookieBanner.style.display = 'none';
            }, 500);
        });
    }
}

/**
 * Initialise le formulaire de demande d'accès aux données
 */
function initDataRequestForm() {
    const forms = document.querySelectorAll('.data-request-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            
            const messageContainer = form.querySelector('.data-request-message');
            if (!messageContainer) return;
            
            // Désactivation du bouton pendant la soumission
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton) return;
            
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Envoi en cours...';
            
            // Préparation des données du formulaire
            const formData = new FormData(form);
            
            // Envoi des données via Fetch API
            fetch(lejournaldesactus_rgpd.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    messageContainer.classList.remove('error');
                    messageContainer.classList.add('success');
                    messageContainer.innerHTML = data.data.message;
                } else {
                    messageContainer.classList.remove('success');
                    messageContainer.classList.add('error');
                    messageContainer.innerHTML = data.data.message;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                messageContainer.classList.remove('success');
                messageContainer.classList.add('error');
                messageContainer.innerHTML = 'Une erreur est survenue. Veuillez réessayer.';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });
    });
}

/**
 * Définir un cookie
 * @param {string} name - Nom du cookie
 * @param {string} value - Valeur du cookie
 * @param {number} days - Durée de vie en jours
 */
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

/**
 * Récupérer la valeur d'un cookie
 * @param {string} name - Nom du cookie
 * @returns {string|null} - Valeur du cookie ou null si non trouvé
 */
function getCookie(name) {
    const cookieName = name + "=";
    const cookies = document.cookie.split(';');
    
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        if (cookie.indexOf(cookieName) === 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
    }
    return null;
}
