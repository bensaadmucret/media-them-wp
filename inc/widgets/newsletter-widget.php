<?php
/**
 * Widget de newsletter
 */

// Empêcher l'accès direct aux fichiers
if (!defined('ABSPATH')) {
    exit; // Sortie si accès direct
}

/**
 * Classe du widget de newsletter
 */
class Lejournaldesactus_Newsletter_Widget extends WP_Widget {

    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(
            'lejournaldesactus_newsletter',
            __('LJDA - Newsletter', 'lejournaldesactus'),
            array(
                'description' => __('Affiche un formulaire d\'inscription à la newsletter.', 'lejournaldesactus'),
                'classname'   => 'widget-advanced widget-newsletter',
            )
        );
    }

    /**
     * Affichage du widget dans le frontend
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Newsletter', 'lejournaldesactus');
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);
        $text = !empty($instance['text']) ? $instance['text'] : __('Inscrivez-vous à notre newsletter pour recevoir les dernières actualités.', 'lejournaldesactus');
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : __('Votre adresse email', 'lejournaldesactus');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('S\'inscrire', 'lejournaldesactus');
        $service = !empty($instance['service']) ? $instance['service'] : 'custom';
        $mailchimp_action = !empty($instance['mailchimp_action']) ? $instance['mailchimp_action'] : '';
        $custom_action = !empty($instance['custom_action']) ? $instance['custom_action'] : admin_url('admin-ajax.php');
        $redirect_url = !empty($instance['redirect_url']) ? $instance['redirect_url'] : '';

        // Afficher le widget
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<div class="newsletter-content">';
        
        if ($text) {
            echo '<div class="newsletter-text">' . esc_html($text) . '</div>';
        }

        // Formulaire d'inscription
        if ($service === 'mailchimp' && !empty($mailchimp_action)) {
            // Formulaire MailChimp
            ?>
            <form action="<?php echo esc_url($mailchimp_action); ?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="newsletter-form validate" target="_blank" novalidate>
                <input type="email" value="" name="EMAIL" class="newsletter-email" id="mce-EMAIL" placeholder="<?php echo esc_attr($placeholder); ?>" required>
                <div style="position: absolute; left: -5000px;" aria-hidden="true">
                    <input type="text" name="b_<?php echo md5(rand()); ?>" tabindex="-1" value="">
                </div>
                <input type="submit" value="<?php echo esc_attr($button_text); ?>" name="subscribe" id="mc-embedded-subscribe" class="newsletter-submit">
            </form>
            <?php
        } else {
            // Formulaire personnalisé
            ?>
            <form action="<?php echo esc_url($custom_action); ?>" method="post" class="newsletter-form">
                <input type="email" name="email" class="newsletter-email" placeholder="<?php echo esc_attr($placeholder); ?>" required>
                <input type="hidden" name="action" value="lejournaldesactus_newsletter_subscribe">
                <?php if ($redirect_url) : ?>
                    <input type="hidden" name="redirect" value="<?php echo esc_url($redirect_url); ?>">
                <?php endif; ?>
                <?php wp_nonce_field('lejournaldesactus_newsletter_nonce', 'newsletter_nonce'); ?>
                <input type="submit" value="<?php echo esc_attr($button_text); ?>" class="newsletter-submit">
            </form>
            <div id="newsletter-response"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('.newsletter-form');
                    const response = document.getElementById('newsletter-response');
                    
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(form);
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                response.innerHTML = '<div class="newsletter-success">' + data.data + '</div>';
                                form.reset();
                                
                                if (formData.get('redirect')) {
                                    setTimeout(function() {
                                        window.location.href = formData.get('redirect');
                                    }, 2000);
                                }
                            } else {
                                response.innerHTML = '<div class="newsletter-error">' + data.data + '</div>';
                            }
                        })
                        .catch(error => {
                            response.innerHTML = '<div class="newsletter-error">Une erreur s\'est produite. Veuillez réessayer.</div>';
                        });
                    });
                });
            </script>
            <?php
        }

        echo '</div>';

        echo $args['after_widget'];
    }

    /**
     * Formulaire d'administration du widget
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Newsletter', 'lejournaldesactus');
        $text = isset($instance['text']) ? $instance['text'] : __('Inscrivez-vous à notre newsletter pour recevoir les dernières actualités.', 'lejournaldesactus');
        $placeholder = isset($instance['placeholder']) ? $instance['placeholder'] : __('Votre adresse email', 'lejournaldesactus');
        $button_text = isset($instance['button_text']) ? $instance['button_text'] : __('S\'inscrire', 'lejournaldesactus');
        $service = isset($instance['service']) ? $instance['service'] : 'custom';
        $mailchimp_action = isset($instance['mailchimp_action']) ? $instance['mailchimp_action'] : '';
        $custom_action = isset($instance['custom_action']) ? $instance['custom_action'] : admin_url('admin-ajax.php');
        $redirect_url = isset($instance['redirect_url']) ? $instance['redirect_url'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Texte:', 'lejournaldesactus'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="3"><?php echo esc_textarea($text); ?></textarea>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>"><?php _e('Placeholder du champ email:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Texte du bouton:', 'lejournaldesactus'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('service'); ?>"><?php _e('Service de newsletter:', 'lejournaldesactus'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('service'); ?>" name="<?php echo $this->get_field_name('service'); ?>">
                <option value="custom" <?php selected($service, 'custom'); ?>><?php _e('Personnalisé', 'lejournaldesactus'); ?></option>
                <option value="mailchimp" <?php selected($service, 'mailchimp'); ?>><?php _e('MailChimp', 'lejournaldesactus'); ?></option>
            </select>
        </p>

        <div class="mailchimp-fields" style="<?php echo $service === 'mailchimp' ? 'display:block;' : 'display:none;'; ?>">
            <p>
                <label for="<?php echo $this->get_field_id('mailchimp_action'); ?>"><?php _e('URL d\'action MailChimp:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('mailchimp_action'); ?>" name="<?php echo $this->get_field_name('mailchimp_action'); ?>" type="text" value="<?php echo esc_attr($mailchimp_action); ?>" />
                <small><?php _e('Ex: https://yourdomain.us1.list-manage.com/subscribe/post?u=...', 'lejournaldesactus'); ?></small>
            </p>
        </div>

        <div class="custom-fields" style="<?php echo $service === 'custom' ? 'display:block;' : 'display:none;'; ?>">
            <p>
                <label for="<?php echo $this->get_field_id('custom_action'); ?>"><?php _e('URL d\'action personnalisée:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('custom_action'); ?>" name="<?php echo $this->get_field_name('custom_action'); ?>" type="text" value="<?php echo esc_attr($custom_action); ?>" />
                <small><?php _e('Laissez vide pour utiliser l\'action AJAX par défaut.', 'lejournaldesactus'); ?></small>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('redirect_url'); ?>"><?php _e('URL de redirection après inscription:', 'lejournaldesactus'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('redirect_url'); ?>" name="<?php echo $this->get_field_name('redirect_url'); ?>" type="text" value="<?php echo esc_attr($redirect_url); ?>" />
                <small><?php _e('Laissez vide pour ne pas rediriger.', 'lejournaldesactus'); ?></small>
            </p>
        </div>

        <script>
            jQuery(document).ready(function($) {
                $('#<?php echo $this->get_field_id('service'); ?>').on('change', function() {
                    if ($(this).val() === 'mailchimp') {
                        $('.mailchimp-fields').show();
                        $('.custom-fields').hide();
                    } else {
                        $('.mailchimp-fields').hide();
                        $('.custom-fields').show();
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * Sauvegarde des options du widget
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['text'] = sanitize_textarea_field($new_instance['text']);
        $instance['placeholder'] = sanitize_text_field($new_instance['placeholder']);
        $instance['button_text'] = sanitize_text_field($new_instance['button_text']);
        $instance['service'] = sanitize_text_field($new_instance['service']);
        $instance['mailchimp_action'] = esc_url_raw($new_instance['mailchimp_action']);
        $instance['custom_action'] = esc_url_raw($new_instance['custom_action']);
        $instance['redirect_url'] = esc_url_raw($new_instance['redirect_url']);

        return $instance;
    }
}
