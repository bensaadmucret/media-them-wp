(function($) {
    // Sécurité : ne rien exécuter si wp.customize absent
    if (typeof wp === 'undefined' || typeof wp.customize !== 'function') {
        // Mais on gère quand même le bouton Export dans le panneau Customizer
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.getElementById('lejournaldesactus-export-btn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Fallback : exporte les couleurs CSS du root si Customizer non dispo
                    const rootStyles = getComputedStyle(document.documentElement);
                    const preset = {
                        lejournaldesactus_primary_color: rootStyles.getPropertyValue('--primary-color').trim(),
                        lejournaldesactus_secondary_color: rootStyles.getPropertyValue('--secondary-color').trim(),
                        lejournaldesactus_bg_color: rootStyles.getPropertyValue('--body-bg').trim(),
                        lejournaldesactus_text_color: rootStyles.getPropertyValue('--text-color').trim(),
                        lejournaldesactus_font_family: rootStyles.getPropertyValue('--font-family-base').replace(', sans-serif','').trim(),
                        lejournaldesactus_font_size: parseInt(rootStyles.getPropertyValue('--font-size-base')) || 16,
                        lejournaldesactus_border_radius: parseInt(rootStyles.getPropertyValue('--border-radius-base')) || 6
                    };
                    const blob = new Blob([JSON.stringify(preset, null, 2)], {type: 'application/json'});
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'lejournaldesactus-preset-' + (new Date().toISOString().slice(0,10)) + '.json';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(() => {
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    }, 100);
                    showToast('Preset exporté (mode fallback)');
                });
            }
        });
        return;
    }

    function isCustomizerContext() {
        return (typeof wp !== 'undefined' && typeof wp.customize === 'function');
    }

    // Utilitaire : injecter ou mettre à jour un <style id="lejournaldesactus-customizer-vars">
    function updateCustomizerVars(vars) {
        let style = document.getElementById('lejournaldesactus-customizer-vars');
        if (!style) {
            style = document.createElement('style');
            style.id = 'lejournaldesactus-customizer-vars';
            document.head.appendChild(style);
        }
        style.textContent = vars;
    }

    // Utilitaire : injecter la police Bunny Fonts dynamiquement
    function updateBunnyFont(font) {
        const fontMap = {
            'Inter': 'inter',
            'Open Sans': 'open-sans',
            'Lato': 'lato',
            'Montserrat': 'montserrat',
            'Roboto': 'roboto',
            'Nunito': 'nunito',
            'Poppins': 'poppins',
            'Raleway': 'raleway',
            'Merriweather': 'merriweather',
            'Space Grotesk': 'space-grotesk',
            'Bricolage Grotesque': 'bricolage-grotesque',
        };
        if (fontMap[font]) {
            let link = document.getElementById('bunny-font-link');
            if (!link) {
                link = document.createElement('link');
                link.id = 'bunny-font-link';
                link.rel = 'stylesheet';
                document.head.prepend(link);
            }
            link.href = 'https://fonts.bunny.net/css?family=' + fontMap[font] + ':400;500;700';
        }
    }

    // Aperçu dynamique de la police et de la taille dans le Customizer
    function updateFontSample() {
        if (!isCustomizerContext()) return;
        let sample = document.getElementById('lejournaldesactus-font-sample');
        if (!sample) {
            sample = document.createElement('div');
            sample.id = 'lejournaldesactus-font-sample';
            sample.style.margin = '16px 0';
            sample.style.padding = '12px 18px';
            sample.style.background = '#f6f6f6';
            sample.style.borderRadius = '8px';
            sample.style.fontWeight = '500';
            sample.innerHTML = 'Exemple de texte : <span style="font-style:italic">Le Journal des Actus</span>';
            // Ajout dans le panneau Customizer
            let ctrl = document.querySelector('[id^="customize-control-lejournaldesactus_font_family"]');
            if (ctrl && ctrl.parentNode) {
                ctrl.parentNode.insertBefore(sample, ctrl.nextSibling);
            }
        }
        // Appliquer les styles dynamiques
        if (typeof wp.customize('lejournaldesactus_font_family') === 'function') {
            let font = wp.customize('lejournaldesactus_font_family')();
            sample.style.fontFamily = font ? font + ', sans-serif' : 'Inter, sans-serif';
        }
        if (typeof wp.customize('lejournaldesactus_font_size') === 'function') {
            let size = wp.customize('lejournaldesactus_font_size')();
            sample.style.fontSize = size ? size + 'px' : '16px';
        }
    }

    // Bind uniquement si Customizer
    if (isCustomizerContext()) {
        wp.customize('lejournaldesactus_primary_color', function(value) {
            value.bind(function(newval) {
                updateVar('--primary-color', newval);
            });
        });
        wp.customize('lejournaldesactus_secondary_color', function(value) {
            value.bind(function(newval) {
                updateVar('--secondary-color', newval);
            });
        });
        wp.customize('lejournaldesactus_bg_color', function(value) {
            value.bind(function(newval) {
                updateVar('--body-bg', newval);
            });
        });
        wp.customize('lejournaldesactus_text_color', function(value) {
            value.bind(function(newval) {
                updateVar('--text-color', newval);
            });
        });
        wp.customize('lejournaldesactus_font_family', function(value) {
            value.bind(function(newval) {
                updateVar('--font-family-base', newval + ', sans-serif');
                updateBunnyFont(newval);
                updateFontSample();
            });
        });
        wp.customize('lejournaldesactus_font_size', function(value) {
            value.bind(function(newval) {
                updateVar('--font-size-base', parseInt(newval, 10) + 'px');
                updateFontSample();
            });
        });
        wp.customize('lejournaldesactus_border_radius', function(value) {
            value.bind(function(newval) {
                updateVar('--border-radius-base', parseInt(newval, 10) + 'px');
            });
        });
    }

    // Mise à jour d'une variable CSS root
    function updateVar(varName, value) {
        let style = document.getElementById('lejournaldesactus-customizer-vars');
        let css = style ? style.textContent : '';
        // Remplace ou ajoute la variable
        const regex = new RegExp(varName + '\\s*:[^;]+;');
        if (css.match(regex)) {
            css = css.replace(regex, varName + ': ' + value + ';');
        } else {
            css = css.replace(/(:root\s*{)/, '$1\n    ' + varName + ': ' + value + ';');
        }
        updateCustomizerVars(css);
    }

    // On ne garde que l'import de preset design côté Customizer
    window.lejournaldesactusImportPreset = function(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const preset = JSON.parse(e.target.result);
                const keys = [
                    'lejournaldesactus_primary_color',
                    'lejournaldesactus_secondary_color',
                    'lejournaldesactus_bg_color',
                    'lejournaldesactus_text_color',
                    'lejournaldesactus_font_family',
                    'lejournaldesactus_font_size',
                    'lejournaldesactus_border_radius'
                ];
                let valid = keys.every(k => k in preset);
                if (!valid) throw new Error('Fichier de preset invalide.');
                keys.forEach(k => {
                    if (typeof wp !== 'undefined' && typeof wp.customize === 'function' && wp.customize(k)) {
                        wp.customize(k).set(preset[k]);
                    }
                });
                showToast('Preset importé avec succès !');
            } catch (err) {
                showToast('Erreur à l\'import : ' + err.message, true);
            }
        };
        reader.readAsText(file);
    };

    // Toast feedback UX (inchangé)
    function showToast(msg, isError) {
        let toast = document.getElementById('lejournaldesactus-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'lejournaldesactus-toast';
            toast.style.position = 'fixed';
            toast.style.top = '24px';
            toast.style.right = '24px';
            toast.style.zIndex = 99999;
            toast.style.padding = '16px 28px';
            toast.style.borderRadius = '8px';
            toast.style.background = isError ? '#f75815' : '#1abc9c';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.boxShadow = '0 2px 16px 0 rgba(40,40,60,0.13)';
            toast.style.fontSize = '1.1rem';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.background = isError ? '#f75815' : '#1abc9c';
        toast.style.opacity = '1';
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
        }, 2200);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (isCustomizerContext()) updateFontSample();
    });
})(jQuery);
