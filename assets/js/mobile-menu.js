/**
 * Mobile Menu Functionality
 * Handles the mobile menu toggle and navigation
 */

// Function to toggle mobile menu
function toggleMobileMenu() {
  document.querySelector('body').classList.toggle('mobile-nav-active');
  
  // Toggle the mobile nav overlay
  const overlay = document.querySelector('.mobile-nav-overlay');
  if (overlay) {
    overlay.classList.toggle('active');
  }
  
  // Toggle the icon between hamburger and X
  const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
  if (mobileNavToggle) {
    mobileNavToggle.classList.toggle('bi-list');
    mobileNavToggle.classList.toggle('bi-x');
  }
}

// Close mobile menu when clicking outside
document.addEventListener('DOMContentLoaded', function() {
  // Close menu when clicking on the overlay
  const overlay = document.querySelector('.mobile-nav-overlay');
  if (overlay) {
    overlay.addEventListener('click', function() {
      if (document.querySelector('body').classList.contains('mobile-nav-active')) {
        toggleMobileMenu();
      }
    });
  }
  
  // Close menu when clicking on the X button
  const closeBtn = document.querySelector('.mobile-menu-close');
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      if (document.querySelector('body').classList.contains('mobile-nav-active')) {
        toggleMobileMenu();
      }
    });
  }
  
  // Close menu when clicking on a menu item
  const menuItems = document.querySelectorAll('#navmenu a');
  menuItems.forEach(item => {
    item.addEventListener('click', function() {
      if (document.querySelector('body').classList.contains('mobile-nav-active')) {
        toggleMobileMenu();
      }
    });
  });
});
