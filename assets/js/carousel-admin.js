/**
 * Script d'administration pour les carrousels
 */
(function($) {
    'use strict';

    // Variables globales
    var frame;
    var slideIndex = $('.carousel-slide').length;

    // Initialisation
    $(document).ready(function() {
        initCarouselAdmin();
    });

    /**
     * Initialiser l'interface d'administration des carrousels
     */
    function initCarouselAdmin() {
        // Ajouter une slide
        $('#add-slide').on('click', function() {
            addSlide();
        });

        // Supprimer une slide (délégation d'événement)
        $('.slides-container').on('click', '.remove-slide', function() {
            $(this).closest('.carousel-slide').remove();
            updateSlideNumbers();
        });

        // Réduire/Développer une slide (délégation d'événement)
        $('.slides-container').on('click', '.slide-toggle', function() {
            $(this).closest('.carousel-slide').find('.slide-content').slideToggle();
        });

        // Choisir une image (délégation d'événement)
        $('.slides-container').on('click', '.upload-image', function() {
            var $button = $(this);
            var $slide = $button.closest('.carousel-slide');
            openMediaUploader($button, $slide);
        });

        // Supprimer une image (délégation d'événement)
        $('.slides-container').on('click', '.remove-image', function() {
            var $slide = $(this).closest('.carousel-slide');
            removeImage($slide);
        });

        // Rendre les slides triables
        $('.slides-container').sortable({
            handle: '.slide-header',
            update: function() {
                updateSlideNumbers();
            }
        });
    }

    /**
     * Ajouter une nouvelle slide
     */
    function addSlide() {
        var template = wp.template('carousel-slide');
        var data = {
            index: slideIndex
        };
        
        $('.slides-container').append(template(data));
        slideIndex++;
        updateSlideNumbers();
    }

    /**
     * Mettre à jour les numéros des slides
     */
    function updateSlideNumbers() {
        $('.carousel-slide').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.slide-number').text(index + 1);
            
            // Mettre à jour les noms des champs
            $(this).find('input, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    name = name.replace(/carousel_slides\[\d+\]/, 'carousel_slides[' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
    }

    /**
     * Ouvrir le sélecteur de médias WordPress
     */
    function openMediaUploader($button, $slide) {
        // Si le sélecteur de médias existe déjà, l'ouvrir
        if (frame) {
            frame.open();
            return;
        }

        // Créer un nouveau sélecteur de médias
        frame = wp.media({
            title: 'Sélectionner ou télécharger une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });

        // Lorsqu'une image est sélectionnée
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            // Mettre à jour l'aperçu de l'image
            var $preview = $slide.find('.image-preview-wrapper');
            $preview.html('<img src="' + attachment.url + '" alt="" style="max-width:100%;">');
            
            // Mettre à jour l'ID de l'image
            $slide.find('.slide-image-id').val(attachment.id);
            
            // Afficher le bouton de suppression
            if (!$slide.find('.remove-image').length) {
                $button.after('<button type="button" class="button remove-image">Supprimer l\'image</button>');
            }
        });

        // Ouvrir le sélecteur de médias
        frame.open();
    }

    /**
     * Supprimer une image
     */
    function removeImage($slide) {
        // Vider l'aperçu de l'image
        $slide.find('.image-preview-wrapper').empty();
        
        // Réinitialiser l'ID de l'image
        $slide.find('.slide-image-id').val('');
        
        // Supprimer le bouton de suppression
        $slide.find('.remove-image').remove();
    }

})(jQuery);
