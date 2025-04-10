<?php
/**
 * Custom template tags for this theme
 *
 * @package LeJournalDesActus
 */

if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Affiche la date de publication formatée
 */
if (!function_exists('lejournaldesactus_posted_on')) :
    function lejournaldesactus_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        echo '<span class="posted-on">' . $time_string . '</span>';
    }
endif;

/**
 * Affiche l'auteur de l'article
 */
if (!function_exists('lejournaldesactus_posted_by')) :
    function lejournaldesactus_posted_by() {
        echo '<span class="byline"> ' . 
             sprintf(
                 /* translators: %s: post author. */
                 esc_html_x('par %s', 'post author', 'lejournaldesactus'),
                 '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
             ) . 
             '</span>';
    }
endif;

/**
 * Affiche les catégories de l'article
 */
if (!function_exists('lejournaldesactus_entry_categories')) :
    function lejournaldesactus_entry_categories() {
        // Récupérer les catégories
        $categories_list = get_the_category_list(', ');
        if ($categories_list) {
            echo '<span class="cat-links">' . $categories_list . '</span>';
        }
    }
endif;

/**
 * Affiche les tags de l'article
 */
if (!function_exists('lejournaldesactus_entry_tags')) :
    function lejournaldesactus_entry_tags() {
        // Récupérer les tags
        $tags_list = get_the_tag_list('', ', ');
        if ($tags_list) {
            echo '<span class="tags-links">' . $tags_list . '</span>';
        }
    }
endif;

/**
 * Affiche un lien de commentaires
 */
if (!function_exists('lejournaldesactus_comment_link')) :
    function lejournaldesactus_comment_link() {
        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Laisser un commentaire <span class="screen-reader-text">sur %s</span>', 'lejournaldesactus'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                )
            );
            echo '</span>';
        }
    }
endif;

/**
 * Affiche l'image mise en avant
 */
if (!function_exists('lejournaldesactus_post_thumbnail')) :
    function lejournaldesactus_post_thumbnail($size = 'post-thumbnail') {
        if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
            return;
        }

        if (is_singular()) :
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail($size, array('class' => 'img-fluid')); ?>
            </div>
        <?php else : ?>
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    $size,
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                        'class' => 'img-fluid',
                    )
                );
                ?>
            </a>
        <?php
        endif;
    }
endif;

/**
 * Affiche la pagination des articles
 */
if (!function_exists('lejournaldesactus_pagination')) :
    function lejournaldesactus_pagination() {
        the_posts_pagination(
            array(
                'mid_size'  => 2,
                'prev_text' => sprintf(
                    '%s <span class="nav-prev-text">%s</span>',
                    '<i class="fas fa-arrow-left"></i>',
                    __('Précédent', 'lejournaldesactus')
                ),
                'next_text' => sprintf(
                    '<span class="nav-next-text">%s</span> %s',
                    __('Suivant', 'lejournaldesactus'),
                    '<i class="fas fa-arrow-right"></i>'
                ),
                'class'     => 'pagination',
            )
        );
    }
endif;
