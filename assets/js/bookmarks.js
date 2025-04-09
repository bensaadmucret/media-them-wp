/**
 * Script pour gérer les bookmarks/favoris
 */
(function($) {
    'use strict';
    
    // Variables
    var notificationTimeout;
    
    // Initialisation
    $(document).ready(function() {
        initBookmarkButtons();
        initBookmarkRemoveButtons();
        checkBookmarksOnLoad();
    });
    
    /**
     * Initialiser les boutons de bookmark
     */
    function initBookmarkButtons() {
        $('.bookmark-button').on('click', function() {
            var $button = $(this);
            var postId = $button.data('post-id');
            
            // Ajouter une classe d'animation
            $button.addClass('animate');
            setTimeout(function() {
                $button.removeClass('animate');
            }, 500);
            
            // Si l'utilisateur n'est pas connecté et que les bookmarks pour visiteurs ne sont pas activés
            if (!lejournaldesactusBookmarks.loggedIn && !lejournaldesactusBookmarks.allowGuestBookmarks) {
                showNotification(lejournaldesactusBookmarks.loginRequired, 'info');
                
                // Rediriger vers la page de connexion après un délai
                setTimeout(function() {
                    window.location.href = lejournaldesactusBookmarks.loginUrl;
                }, 2000);
                
                return;
            }
            
            // Appel AJAX pour ajouter/retirer le bookmark
            $.ajax({
                url: lejournaldesactusBookmarks.ajaxurl,
                type: 'POST',
                data: {
                    action: lejournaldesactusBookmarks.loggedIn ? 'lejournaldesactus_toggle_bookmark' : 'lejournaldesactus_toggle_bookmark_guest',
                    post_id: postId,
                    nonce: lejournaldesactusBookmarks.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Mettre à jour l'apparence du bouton
                        if (response.data.status === 'added') {
                            $button.addClass('bookmarked');
                            $button.find('i').removeClass('bi-bookmark').addClass('bi-bookmark-fill');
                            $button.find('.bookmark-text').text(lejournaldesactusBookmarks.bookmarkedText || 'Enregistré');
                            
                            // Afficher la notification
                            showNotification(lejournaldesactusBookmarks.loggedIn ? lejournaldesactusBookmarks.bookmarkAdded : lejournaldesactusBookmarks.guestBookmarkAdded, 'success');
                        } else {
                            $button.removeClass('bookmarked');
                            $button.find('i').removeClass('bi-bookmark-fill').addClass('bi-bookmark');
                            $button.find('.bookmark-text').text(lejournaldesactusBookmarks.bookmarkText || 'Enregistrer');
                            
                            // Afficher la notification
                            showNotification(lejournaldesactusBookmarks.loggedIn ? lejournaldesactusBookmarks.bookmarkRemoved : lejournaldesactusBookmarks.guestBookmarkRemoved, 'success');
                        }
                        
                        // Mettre à jour le compteur dans le menu
                        updateBookmarkCount(response.data.count);
                    } else {
                        // Afficher l'erreur
                        showNotification(response.data, 'error');
                    }
                },
                error: function() {
                    showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
                }
            });
        });
    }
    
    /**
     * Initialiser les boutons de suppression de bookmark sur la page des favoris
     */
    function initBookmarkRemoveButtons() {
        $('.bookmark-remove').on('click', function() {
            var $button = $(this);
            var postId = $button.data('post-id');
            var $bookmarkItem = $button.closest('.bookmark-item');
            
            // Appel AJAX pour retirer le bookmark
            $.ajax({
                url: lejournaldesactusBookmarks.ajaxurl,
                type: 'POST',
                data: {
                    action: lejournaldesactusBookmarks.loggedIn ? 'lejournaldesactus_toggle_bookmark' : 'lejournaldesactus_toggle_bookmark_guest',
                    post_id: postId,
                    nonce: lejournaldesactusBookmarks.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Animer la suppression de l'élément
                        $bookmarkItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Vérifier s'il reste des favoris
                            if ($('.bookmark-item').length === 0) {
                                // Aucun favori restant, afficher le message vide
                                var emptyMessage = '<div class="bookmarks-empty">' +
                                    '<p>' + (lejournaldesactusBookmarks.emptyMessage || 'Vous n\'avez pas encore d\'articles favoris.') + '</p>' +
                                    '<a href="' + lejournaldesactusBookmarks.homeUrl + '" class="btn btn-primary">Parcourir les articles</a>' +
                                    '</div>';
                                
                                $('.bookmarks-list').replaceWith(emptyMessage);
                            }
                        });
                        
                        // Afficher la notification
                        showNotification(lejournaldesactusBookmarks.loggedIn ? lejournaldesactusBookmarks.bookmarkRemoved : lejournaldesactusBookmarks.guestBookmarkRemoved, 'success');
                        
                        // Mettre à jour le compteur dans le menu
                        updateBookmarkCount(response.data.count);
                    } else {
                        // Afficher l'erreur
                        showNotification(response.data, 'error');
                    }
                },
                error: function() {
                    showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
                }
            });
        });
    }
    
    /**
     * Afficher une notification
     */
    function showNotification(message, type) {
        // Supprimer les notifications existantes
        $('.bookmark-notification').remove();
        clearTimeout(notificationTimeout);
        
        // Créer la notification
        var $notification = $('<div class="bookmark-notification ' + type + '">' + message + '</div>');
        $('body').append($notification);
        
        // Afficher la notification
        setTimeout(function() {
            $notification.addClass('show');
        }, 10);
        
        // Masquer la notification après 3 secondes
        notificationTimeout = setTimeout(function() {
            $notification.removeClass('show');
            
            // Supprimer la notification après l'animation
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
    /**
     * Mettre à jour le compteur de bookmarks dans le menu
     */
    function updateBookmarkCount(count) {
        var $menuItem = $('.menu-item-bookmarks');
        var $count = $menuItem.find('.bookmark-count');
        
        if (count > 0) {
            if ($count.length) {
                $count.text(count);
            } else {
                $menuItem.find('a').append('<span class="bookmark-count">' + count + '</span>');
            }
            // S'assurer que l'élément de menu est visible
            $menuItem.removeClass('d-none').fadeIn(300);
        } else {
            $count.remove();
            // Masquer l'élément de menu s'il n'y a plus de favoris
            $menuItem.fadeOut(300, function() {
                $(this).addClass('d-none');
            });
        }
    }
    
    /**
     * Vérifier s'il y a des favoris au chargement de la page
     * et masquer le menu si nécessaire
     */
    function checkBookmarksOnLoad() {
        var $menuItem = $('.menu-item-bookmarks');
        var $count = $menuItem.find('.bookmark-count');
        
        if ($count.length) {
            // Il y a un compteur, vérifier s'il y a des favoris
            var count = parseInt($count.text());
            if (count <= 0) {
                $menuItem.addClass('d-none');
            }
        } else {
            // Pas de compteur, vérifier si l'utilisateur a des favoris
            $.ajax({
                url: lejournaldesactusBookmarks.ajaxurl,
                type: 'POST',
                data: {
                    action: 'lejournaldesactus_check_bookmarks',
                    nonce: lejournaldesactusBookmarks.nonce
                },
                success: function(response) {
                    if (response.success && response.data.count <= 0) {
                        $menuItem.addClass('d-none');
                    }
                }
            });
        }
    }
    
})(jQuery);
