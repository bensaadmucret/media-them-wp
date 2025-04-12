<?php
/**
 * Template part pour afficher le contenu d'un article
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="featured-image-wrapper article-featured-image">
            <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
            <?php 
            $credit = get_image_credit();
            if ($credit) : ?>
                <div class="featured-image-credit">
                    <?php echo esc_html($credit); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <header class="entry-header article-header">
        <?php the_title('<h1 class="entry-title article-title">', '</h1>'); ?>
        
        <div class="entry-meta article-meta">
            <?php
            // Date de publication
            echo '<span class="posted-on">';
            echo '<i class="bi bi-calendar"></i> ';
            echo get_the_date();
            echo '</span>';
            
            // Auteur
            echo '<span class="byline">';
            echo '<i class="bi bi-person"></i> ';
            echo get_the_author();
            echo '</span>';
            
            // Catégories
            $categories_list = get_the_category_list(', ');
            if ($categories_list) {
                echo '<span class="cat-links">';
                echo '<i class="bi bi-folder"></i> ';
                echo $categories_list;
                echo '</span>';
            }
            ?>
        </div>
    </header>

    <div class="entry-content article-body">
        <?php
        the_content();
        
        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'lejournaldesactus'),
            'after'  => '</div>',
        ));
        ?>
    </div>

    <footer class="entry-footer article-footer">
        <?php
        $tags_list = get_the_tag_list('', ', ');
        if ($tags_list) {
            echo '<div class="tags-links">';
            echo '<i class="bi bi-tags"></i> ';
            echo $tags_list;
            echo '</div>';
        }
        ?>
    </footer>
</article>
