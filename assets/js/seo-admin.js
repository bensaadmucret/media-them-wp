/**
 * Script pour l'interface d'administration SEO
 *
 * @package LeJournalDesActus
 */

(function($) {
    'use strict';

    // Initialisation au chargement du DOM
    $(document).ready(function() {
        // Mise à jour des compteurs de caractères
        updateCharCount('#seo_title', '#seo-title-count', 60);
        updateCharCount('#seo_description', '#seo-description-count', 160);
        
        // Mise à jour de l'aperçu en temps réel
        updatePreview();
        
        // Écouteurs d'événements
        $('#seo_title').on('input', function() {
            updateCharCount('#seo_title', '#seo-title-count', 60);
            updatePreview();
        });
        
        $('#seo_description').on('input', function() {
            updateCharCount('#seo_description', '#seo-description-count', 160);
            updatePreview();
        });
        
        // Analyse du contenu de l'article
        analyzeContent();
    });
    
    /**
     * Mettre à jour le compteur de caractères
     */
    function updateCharCount(inputSelector, countSelector, limit) {
        var length = $(inputSelector).val().length;
        $(countSelector).text(length + '/' + limit);
        
        if (length > limit) {
            $(countSelector).addClass('over-limit');
        } else {
            $(countSelector).removeClass('over-limit');
        }
    }
    
    /**
     * Mettre à jour l'aperçu en temps réel
     */
    function updatePreview() {
        var title = $('#seo_title').val();
        var description = $('#seo_description').val();
        var defaultTitle = $('input[name="post_title"]').val();
        var defaultContent = '';
        
        // Utiliser le titre par défaut si le titre SEO est vide
        if (!title) {
            title = defaultTitle;
        }
        
        // Utiliser le contenu par défaut si la description SEO est vide
        if (!description && typeof lejournaldesactusSEO !== 'undefined') {
            defaultContent = lejournaldesactusSEO.content;
            description = defaultContent.substring(0, 160);
            
            // Supprimer les balises HTML
            description = $('<div>').html(description).text();
            
            // Ajouter des points de suspension si nécessaire
            if (defaultContent.length > 160) {
                description += '...';
            }
        }
        
        // Mettre à jour l'aperçu
        $('#seo-preview-title').text(title);
        $('#seo-preview-description').text(description);
    }
    
    /**
     * Analyser le contenu de l'article
     */
    function analyzeContent() {
        if (typeof lejournaldesactusSEO === 'undefined') {
            return;
        }
        
        var content = lejournaldesactusSEO.content;
        var title = lejournaldesactusSEO.title;
        var focusKeyword = $('#seo_focus_keyword').val();
        
        // Écouter les changements du mot-clé principal
        $('#seo_focus_keyword').on('input', function() {
            focusKeyword = $(this).val();
            checkKeywordUsage(content, title, focusKeyword);
        });
        
        // Vérifier l'utilisation du mot-clé
        checkKeywordUsage(content, title, focusKeyword);
    }
    
    /**
     * Vérifier l'utilisation du mot-clé principal
     */
    function checkKeywordUsage(content, title, keyword) {
        if (!keyword) {
            return;
        }
        
        // Convertir en texte brut
        var plainContent = $('<div>').html(content).text().toLowerCase();
        var plainTitle = title.toLowerCase();
        var lowercaseKeyword = keyword.toLowerCase();
        
        // Vérifier si le mot-clé est présent dans le titre
        var keywordInTitle = plainTitle.indexOf(lowercaseKeyword) !== -1;
        
        // Vérifier si le mot-clé est présent dans le contenu
        var keywordInContent = plainContent.indexOf(lowercaseKeyword) !== -1;
        
        // Calculer la densité du mot-clé
        var keywordCount = 0;
        var position = plainContent.indexOf(lowercaseKeyword);
        
        while (position !== -1) {
            keywordCount++;
            position = plainContent.indexOf(lowercaseKeyword, position + 1);
        }
        
        var wordCount = plainContent.split(/\s+/).length;
        var keywordDensity = (keywordCount / wordCount) * 100;
        
        // Mettre à jour l'interface utilisateur avec les résultats
        // Cette partie peut être développée davantage selon les besoins
        console.log('Mot-clé dans le titre: ' + keywordInTitle);
        console.log('Mot-clé dans le contenu: ' + keywordInContent);
        console.log('Densité du mot-clé: ' + keywordDensity.toFixed(2) + '%');
    }

})(jQuery);
