(function(){
  function toggleStylesheet(id, href, enable) {
    let link = document.getElementById(id);
    if (enable) {
      if (!link) {
        link = document.createElement('link');
        link.rel = 'stylesheet';
        link.id = id;
        link.href = href;
        document.head.appendChild(link);
        console.log('[accessibility-toggles.js] Ajout feuille:', href);
      }
    } else {
      if (link) {
        link.parentNode.removeChild(link);
        console.log('[accessibility-toggles.js] Retrait feuille:', href);
      }
    }
  }
  function setToggleState(btn, className, cssId, cssHref) {
    const active = document.body.classList.toggle(className);
    btn.setAttribute('aria-pressed', active);
    if (active) localStorage.setItem(className, '1');
    else localStorage.removeItem(className);
    toggleStylesheet(cssId, cssHref, active);
    console.log('[accessibility-toggles.js] Toggle:', className, '->', active);
  }
  document.addEventListener('DOMContentLoaded', function() {
    var btnDyslexia = document.getElementById('toggle-dyslexia');
    console.log('[accessibility-toggles.js] Boutons:', btnDyslexia);
    if(btnDyslexia) {
      btnDyslexia.addEventListener('click', function(){ setToggleState(btnDyslexia, 'dyslexia-font', 'css-dyslexique', '/wp-content/themes/lejournaldesactus/assets/css/dyslexique.css'); });
      if(localStorage.getItem('dyslexia-font')) {
        document.body.classList.add('dyslexia-font');
        toggleStylesheet('css-dyslexique', '/wp-content/themes/lejournaldesactus/assets/css/dyslexique.css', true);
      }
    }
  });
})();
