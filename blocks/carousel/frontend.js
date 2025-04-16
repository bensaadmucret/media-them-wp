// Initialisation Swiper côté front pour le bloc Gutenberg
(function() {
  if (typeof Swiper === 'undefined') return;
  document.querySelectorAll('.wp-block-lejournaldesactus-carousel.swiper, .wp-block-lejournaldesactus-carousel.swiper-container').forEach(function(carousel) {
    new Swiper(carousel, {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 24,
      navigation: {
        nextEl: carousel.querySelector('.swiper-button-next'),
        prevEl: carousel.querySelector('.swiper-button-prev'),
      },
      pagination: {
        el: carousel.querySelector('.swiper-pagination'),
        clickable: true
      },
      autoplay: {
        delay: 4000,
        disableOnInteraction: false
      },
    });
    // Ajout des boutons si absents
    if (!carousel.querySelector('.swiper-button-next')) {
      var next = document.createElement('div');
      next.className = 'swiper-button-next';
      carousel.appendChild(next);
    }
    if (!carousel.querySelector('.swiper-button-prev')) {
      var prev = document.createElement('div');
      prev.className = 'swiper-button-prev';
      carousel.appendChild(prev);
    }
    if (!carousel.querySelector('.swiper-pagination')) {
      var pag = document.createElement('div');
      pag.className = 'swiper-pagination';
      carousel.appendChild(pag);
    }
  });
})();
