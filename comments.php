<?php
/**
 * The template for displaying comments
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password,
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h4 class="comments-title">
            <?php
            $blogy_comment_count = get_comments_number();
            if ('1' === $blogy_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('Un commentaire sur &ldquo;%1$s&rdquo;', 'blogy'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx('%1$s commentaire sur &ldquo;%2$s&rdquo;', '%1$s commentaires sur &ldquo;%2$s&rdquo;', $blogy_comment_count, 'comments title', 'blogy')),
                    number_format_i18n($blogy_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h4>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size' => 60,
                )
            );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note.
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Les commentaires sont fermés.', 'blogy'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    comment_form(
        array(
            'class_form'         => 'comment-form',
            'title_reply'        => esc_html__('Laisser un commentaire', 'blogy'),
            'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title">',
            'title_reply_after'  => '</h4>',
        )
    );
    ?>

</div><!-- #comments -->
