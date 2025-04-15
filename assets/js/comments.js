// Toast/Pop-up pour la validation du commentaire trop court
(function() {
  // Configuration
  const MIN_LENGTH = 15;
  const TOAST_DURATION = 3500; // ms
  const TOAST_ID = 'lejournaldesactus-comment-toast';

  function showToast(message) {
    let toast = document.getElementById(TOAST_ID);
    if (!toast) {
      toast = document.createElement('div');
      toast.id = TOAST_ID;
      toast.className = 'lejournaldesactus-toast';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, TOAST_DURATION);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentform');
    if (!commentForm) return;
    const textarea = commentForm.querySelector('textarea[name="comment"]');
    if (!textarea) return;

    // --- Anti-flood : ajoute un timestamp au chargement du formulaire de commentaire ---
    if (!commentForm.querySelector('input[name="lejournaldesactus_ts"]')) {
      const ts = document.createElement('input');
      ts.type = 'hidden';
      ts.name = 'lejournaldesactus_ts';
      ts.value = Math.floor(Date.now() / 1000);
      commentForm.appendChild(ts);
    }

    commentForm.addEventListener('submit', function(e) {
      if (textarea.value.trim().length < MIN_LENGTH) {
        e.preventDefault();
        showToast('Votre commentaire est trop court. Merci de détailler davantage.');
        textarea.focus();
      }
    });
  });

  // --- Affichage toast si erreur serveur (anti-spam/captcha) ---
  (function() {
    function getCommentError() {
      const params = new URLSearchParams(window.location.search);
      return params.get('comment_error');
    }
    function getCaptchaDebug() {
      // Lecture du cookie debug
      const match = document.cookie.match(/lejournal_captcha_debug=([^;]+)/);
      if (match) {
        const val = decodeURIComponent(match[1]);
        const parts = val.split('&');
        const obj = {};
        parts.forEach(p => {
          const [k, v] = p.split('=');
          obj[k] = v;
        });
        return obj;
      }
      return null;
    }
    const error = getCommentError();
    if (error) {
      let msg = '';
      switch (error) {
        case 'captcha':
          msg = 'Erreur : La réponse à la question anti-spam est incorrecte.';
          // Debug : afficher les valeurs attendue/envoyée
          const dbg = getCaptchaDebug();
          if (dbg) {
            msg += ` (attendu: ${dbg.expected}, fourni: ${dbg.provided})`;
          }
          break;
        case 'honeypot':
          msg = 'Erreur : Spam détecté (honeypot).';
          break;
        case 'flood':
          msg = 'Erreur : Vous avez soumis le commentaire trop rapidement.';
          break;
        default:
          msg = 'Erreur lors de la soumission du commentaire.';
      }
      setTimeout(() => {
        showToast(msg);
      }, 300); // Laisse le DOM charger
    }
  })();

  // --- Affichage toast pour toute erreur comment_error_msg ---
  (function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('comment_error_msg')) {
      let msg = decodeURIComponent(params.get('comment_error_msg'));
      setTimeout(() => {
        showToast(msg);
      }, 350);
    }
  })();

  // --- Affichage toast SUCCÈS après ajout commentaire ---
  (function() {
    // Commentaire publié : ancre #comment-XXX
    const match = window.location.hash.match(/^#comment-(\d+)/);
    if (match) {
      setTimeout(() => {
        showToast('Votre commentaire a bien été publié !');
      }, 400);
      return;
    }
    // Commentaire en attente de modération : paramètre ?unapproved=...
    const params = new URLSearchParams(window.location.search);
    if (params.has('unapproved')) {
      setTimeout(() => {
        showToast('Merci, votre commentaire est en attente de modération.');
      }, 400);
      return;
    }
    // Fallback : paramètre ?comment_success=1
    if (params.get('comment_success') === '1') {
      setTimeout(() => {
        showToast('Votre commentaire a bien été publié !');
      }, 400);
    }
  })();
})();
