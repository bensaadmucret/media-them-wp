(function() {
  "use strict";

  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader || (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top'))) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    const body = document.querySelector('body');
    if (body) {
      body.classList.toggle('mobile-nav-active');
    }
    if (mobileNavToggleBtn) {
      mobileNavToggleBtn.classList.toggle('bi-list');
      mobileNavToggleBtn.classList.toggle('bi-x');
    }
  }
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    if (navmenu) {
      navmenu.addEventListener('click', () => {
        const mobileNavActive = document.querySelector('.mobile-nav-active');
        if (mobileNavActive) {
          mobileNavToogle();
        }
      });
    }
  });

  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    if (navmenu) {
      navmenu.addEventListener('click', function(e) {
        e.preventDefault();
        if (this.parentNode) {
          this.parentNode.classList.toggle('active');
        }
        if (this.parentNode && this.parentNode.nextElementSibling) {
          this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
        }
        e.stopImmediatePropagation();
      });
    }
  });

  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  if (scrollTop) {
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  function aosInit() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
    }
  }
  window.addEventListener('load', aosInit);

  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      if (swiperElement) {
        let config = {};
        const configElement = swiperElement.querySelector(".swiper-config");
        if (configElement) {
          try {
            config = JSON.parse(configElement.innerHTML.trim());
          } catch (e) {}
        }
        if (swiperElement.classList.contains("swiper-tab")) {
          if (typeof initSwiperWithCustomPagination === 'function') {
            initSwiperWithCustomPagination(swiperElement, config);
          }
        } else {
          if (typeof Swiper !== 'undefined') {
            new Swiper(swiperElement, config);
          }
        }
      }
    });
  }

  window.addEventListener("load", initSwiper);

  if (typeof PureCounter === 'function') {
    try {
      new PureCounter();
    } catch (e) {}
  }

  // if (typeof GLightbox === 'function') {
  //   GLightbox({
  //     selector: '.glightbox'
  //   });
  // }

})();