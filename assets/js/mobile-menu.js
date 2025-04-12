/**
 * Mobile Menu Functionality
 * Handles the mobile menu toggle and navigation
 */

// Variable globale pour suivre l'état du menu
let mobileMenuOpen = false;

// Fonction pour vérifier si le mode sombre est actif
function isDarkModeActive() {
  return document.documentElement.getAttribute('data-theme') === 'dark' || 
         document.body.classList.contains('dark-mode');
}

// Fonction pour ouvrir le menu mobile
function openMobileMenu() {
  console.log('Ouverture du menu mobile');
  
  // Vérifier si le mode sombre est actif
  const darkMode = isDarkModeActive();
  console.log('Mode sombre actif:', darkMode);
  
  // Ajouter la classe au body
  document.body.classList.add('mobile-nav-active');
  
  // Activer l'overlay
  const overlay = document.querySelector('.mobile-nav-overlay');
  if (overlay) {
    overlay.classList.add('active');
    if (darkMode) {
      overlay.classList.add('dark-mode-overlay');
    }
  }
  
  // Changer l'icône du bouton
  const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
  if (mobileNavToggle) {
    // Changer l'icône du hamburger en X
    mobileNavToggle.classList.remove('bi-list');
    mobileNavToggle.classList.add('bi-x');
    
    // Créer un élément de style pour forcer la couleur
    let styleElement = document.getElementById('mobile-menu-style');
    if (!styleElement) {
      styleElement = document.createElement('style');
      styleElement.id = 'mobile-menu-style';
      document.head.appendChild(styleElement);
    }
    
    // Définir les styles en fonction du mode
    if (darkMode) {
      mobileNavToggle.classList.add('dark-mode-icon');
      // Injecter des règles CSS directement
      styleElement.textContent = `
        .mobile-nav-toggle {
          color: #ffffff !important;
          text-shadow: 0 0 3px rgba(0, 0, 0, 0.7) !important;
        }
      `;
    } else {
      mobileNavToggle.classList.remove('dark-mode-icon');
      // Injecter des règles CSS directement
      styleElement.textContent = `
        .mobile-nav-toggle {
          color: #000000 !important;
          text-shadow: 0 0 3px rgba(255, 255, 255, 0.7) !important;
        }
      `;
    }
  }
  
  // Afficher le menu
  const navWrap = document.querySelector('.nav-wrap');
  if (navWrap) {
    navWrap.classList.add('open');
  }
  
  // Adapter la couleur des liens selon le mode
  const menuLinks = document.querySelectorAll('.mobile-menu-list a');
  menuLinks.forEach(link => {
    if (darkMode) {
      link.classList.add('dark-mode-link');
    } else {
      link.classList.remove('dark-mode-link');
    }
  });
  
  // Appliquer des styles au titre h3 en mode sombre
  const menuHeader = document.querySelector('.mobile-menu-header h3');
  if (menuHeader) {
    if (darkMode) {
      menuHeader.classList.add('dark-mode-title');
    } else {
      menuHeader.classList.remove('dark-mode-title');
    }
  }
  
  // Appliquer des styles au menu et aux liens sociaux
  const mobileMenuList = document.querySelector('.mobile-menu-list');
  if (mobileMenuList && darkMode) {
    mobileMenuList.classList.add('dark-mode-menu');
  } else if (mobileMenuList) {
    mobileMenuList.classList.remove('dark-mode-menu');
  }
  
  const mobileSocialLinks = document.querySelector('.mobile-social-links');
  if (mobileSocialLinks && darkMode) {
    mobileSocialLinks.classList.add('dark-mode-social');
  } else if (mobileSocialLinks) {
    mobileSocialLinks.classList.remove('dark-mode-social');
  }
  
  // Mettre à jour l'état
  mobileMenuOpen = true;
  console.log('Menu mobile ouvert');
}

// Fonction pour fermer le menu mobile
function closeMobileMenu() {
  console.log('Fermeture du menu mobile');
  
  // Retirer la classe du body
  document.body.classList.remove('mobile-nav-active');
  
  // Désactiver l'overlay
  const overlay = document.querySelector('.mobile-nav-overlay');
  if (overlay) {
    overlay.classList.remove('active');
    overlay.classList.remove('dark-mode-overlay');
  }
  
  // Réinitialiser l'icône du bouton
  const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
  if (mobileNavToggle) {
    // Restaurer l'icône hamburger quand le menu est fermé
    mobileNavToggle.classList.remove('bi-x');
    mobileNavToggle.classList.add('bi-list');
    mobileNavToggle.classList.remove('dark-mode-icon');
    mobileNavToggle.style.color = '';
    mobileNavToggle.style.textShadow = '';
    
    // Supprimer l'élément de style
    const styleElement = document.getElementById('mobile-menu-style');
    if (styleElement) {
      styleElement.textContent = '';
    }
  }
  
  // Masquer le menu
  const navWrap = document.querySelector('.nav-wrap');
  if (navWrap) {
    navWrap.classList.remove('open');
  }
  
  // Réinitialiser la couleur des liens
  const menuLinks = document.querySelectorAll('.mobile-menu-list a');
  menuLinks.forEach(link => {
    link.classList.remove('dark-mode-link');
  });
  
  // Réinitialiser les classes de mode sombre
  const menuHeader = document.querySelector('.mobile-menu-header h3');
  if (menuHeader) {
    menuHeader.classList.remove('dark-mode-title');
  }
  
  const mobileMenuList = document.querySelector('.mobile-menu-list');
  if (mobileMenuList) {
    mobileMenuList.classList.remove('dark-mode-menu');
  }
  
  const mobileSocialLinks = document.querySelector('.mobile-social-links');
  if (mobileSocialLinks) {
    mobileSocialLinks.classList.remove('dark-mode-social');
  }
  
  // Mettre à jour l'état
  mobileMenuOpen = false;
  console.log('Menu mobile fermé');
}

// Fonction pour basculer l'état du menu mobile
function toggleMobileMenu() {
  console.log('Toggle du menu mobile, état actuel:', mobileMenuOpen ? 'ouvert' : 'fermé');
  
  if (mobileMenuOpen) {
    closeMobileMenu();
  } else {
    openMobileMenu();
  }
}

// Initialiser les événements lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
  console.log('Initialisation du menu mobile');
  
  // S'assurer que le menu est fermé au chargement
  closeMobileMenu();
  
  // Attacher l'événement de clic au bouton du menu
  const mobileMenuBtn = document.querySelector('.mobile-nav-toggle');
  if (mobileMenuBtn) {
    console.log('Bouton de menu mobile trouvé');
    mobileMenuBtn.addEventListener('click', function(e) {
      console.log('Clic sur le bouton du menu mobile');
      e.preventDefault();
      e.stopPropagation();
      toggleMobileMenu();
    });
  } else {
    console.error('Bouton de menu mobile non trouvé');
  }
  
  // Attacher l'événement de clic à la croix de fermeture
  const mobileMenuClose = document.querySelector('.mobile-menu-close');
  if (mobileMenuClose) {
    console.log('Bouton de fermeture du menu mobile trouvé');
    mobileMenuClose.addEventListener('click', function(e) {
      console.log('Clic sur la croix de fermeture');
      e.preventDefault();
      e.stopPropagation();
      closeMobileMenu();
    });
  } else {
    console.error('Bouton de fermeture du menu mobile non trouvé');
  }
  
  // Fermer le menu lors d'un clic sur un élément du menu
  const menuItems = document.querySelectorAll('.mobile-menu-list a');
  menuItems.forEach(item => {
    item.addEventListener('click', function() {
      if (mobileMenuOpen) {
        closeMobileMenu();
      }
    });
  });
  
  // Créer un gestionnaire de clic sur le document pour fermer le menu
  document.addEventListener('click', function(e) {
    // Si le menu est ouvert et que le clic n'est pas sur un élément du menu
    if (mobileMenuOpen) {
      const navMenuUl = document.querySelector('.mobile-menu-list');
      const mobileLinks = document.querySelector('.mobile-social-links');
      const mobileMenuBtn = document.querySelector('.mobile-nav-toggle');
      
      // Vérifier si le clic est en dehors du menu et du bouton
      if (navMenuUl && !navMenuUl.contains(e.target) && 
          mobileLinks && !mobileLinks.contains(e.target) &&
          mobileMenuBtn && !mobileMenuBtn.contains(e.target)) {
        closeMobileMenu();
      }
    }
  });
  
  // Écouter les changements de thème pour mettre à jour le menu si ouvert
  document.addEventListener('themeChanged', function() {
    if (mobileMenuOpen) {
      // Réappliquer les styles adaptés au thème actuel
      openMobileMenu();
    }
  });
});
