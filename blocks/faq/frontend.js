// Accordéon FAQ vanilla JS
(function(){
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.wp-block-lejournaldesactus-faq .faq-question').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const answer = btn.parentElement.querySelector('.faq-answer');
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        // Fermer tous les autres si on veut un accordéon strict :
        document.querySelectorAll('.wp-block-lejournaldesactus-faq .faq-question').forEach(function(b) {
          if (b !== btn) {
            b.setAttribute('aria-expanded', 'false');
            const a = b.parentElement.querySelector('.faq-answer');
            if(a) a.setAttribute('hidden', '');
          }
        });
        // Toggle celui cliqué
        btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if(answer) {
          if(expanded) {
            answer.setAttribute('hidden', '');
          } else {
            answer.removeAttribute('hidden');
          }
        }
      });
    });
  });
})();
