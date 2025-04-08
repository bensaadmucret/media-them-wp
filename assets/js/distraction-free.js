/**
 * Distraction Free Reading Mode
 * 
 * Ce script gère le mode de lecture sans distraction pour les articles
 * Il permet aux utilisateurs de basculer vers une vue épurée de l'article
 * en masquant les éléments non essentiels à la lecture.
 */

document.addEventListener('DOMContentLoaded', function() {
  // Sélectionner le bouton de lecture sans distraction
  const distractionFreeButton = document.querySelector('.distraction-free-toggle');
  
  if (!distractionFreeButton) return;
  
  // Éléments à masquer en mode lecture sans distraction
  const elementsToHide = [
    'header',
    '.sidebar',
    '.post-navigation',
    '.post-share',
    '.post-footer',
    // '.related-posts', // Ne pas masquer les articles similaires
    '.blog-comments',
    'footer',
    '.post-meta'
  ];
  
  // Éléments à modifier en mode lecture sans distraction
  const articleContent = document.querySelector('.blog-details-content');
  const mainContainer = document.querySelector('.blog-details .container');
  const articleTitle = document.querySelector('.blog-details-content .title');
  const relatedPosts = document.querySelector('.related-posts');
  
  // État du mode lecture sans distraction
  let isDistractionFreeMode = false;
  
  // Fonction pour basculer le mode lecture sans distraction
  function toggleDistractionFreeMode() {
    isDistractionFreeMode = !isDistractionFreeMode;
    
    // Mettre à jour l'icône et le texte du bouton
    if (isDistractionFreeMode) {
      distractionFreeButton.innerHTML = '<i class="bi bi-arrows-angle-contract"></i> Mode normal';
      distractionFreeButton.classList.add('active');
      document.body.classList.add('distraction-free-mode');
      
      // Masquer les éléments non essentiels
      elementsToHide.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
          if (el) el.classList.add('df-hidden');
        });
      });
      
      // Modifier le style de l'article pour une meilleure lisibilité
      if (articleContent) {
        articleContent.classList.add('df-content');
      }
      
      if (mainContainer) {
        mainContainer.classList.add('df-container');
      }
      
      if (articleTitle) {
        articleTitle.classList.add('df-title');
      }
      
      // Appliquer un style spécial aux articles similaires en mode lecture sans distraction
      if (relatedPosts) {
        relatedPosts.classList.add('df-related-posts');
      }
      
      // Faire défiler jusqu'au début de l'article
      if (articleTitle) {
        articleTitle.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      
      // Sauvegarder la préférence utilisateur
      localStorage.setItem('distractionFreeMode', 'enabled');
    } else {
      distractionFreeButton.innerHTML = '<i class="bi bi-arrows-angle-expand"></i> Lecture zen';
      distractionFreeButton.classList.remove('active');
      document.body.classList.remove('distraction-free-mode');
      
      // Réafficher les éléments masqués
      elementsToHide.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
          if (el) el.classList.remove('df-hidden');
        });
      });
      
      // Restaurer le style de l'article
      if (articleContent) {
        articleContent.classList.remove('df-content');
      }
      
      if (mainContainer) {
        mainContainer.classList.remove('df-container');
      }
      
      if (articleTitle) {
        articleTitle.classList.remove('df-title');
      }
      
      // Restaurer le style normal des articles similaires
      if (relatedPosts) {
        relatedPosts.classList.remove('df-related-posts');
      }
      
      // Sauvegarder la préférence utilisateur
      localStorage.setItem('distractionFreeMode', 'disabled');
    }
  }
  
  // Ajouter l'écouteur d'événement au bouton
  distractionFreeButton.addEventListener('click', function(e) {
    e.preventDefault();
    toggleDistractionFreeMode();
  });
  
  // Vérifier si l'utilisateur avait précédemment activé le mode lecture sans distraction
  const savedMode = localStorage.getItem('distractionFreeMode');
  if (savedMode === 'enabled') {
    toggleDistractionFreeMode();
  }
  
  // Ajouter un raccourci clavier (Alt+Z) pour basculer le mode
  document.addEventListener('keydown', function(e) {
    // Alt+Z pour basculer le mode lecture sans distraction
    if (e.altKey && e.key === 'z') {
      toggleDistractionFreeMode();
    }
    
    // Échap pour quitter le mode lecture sans distraction
    if (e.key === 'Escape' && isDistractionFreeMode) {
      toggleDistractionFreeMode();
    }
  });
});
