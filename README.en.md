# Le Journal des Actus - WordPress Theme (English)

A custom WordPress theme for "Le Journal des Actus" with integrated newsletter and GDPR compliance, designed for optimal user experience and efficient content management.

## Main Features

### Advanced Newsletter System
- **Double opt-in**: GDPR-compliant with email confirmation
- **Customizable GDPR consent text** via admin panel
- **Hybrid delivery system**:
  - Weekly digest of new articles (automatic)
  - Optional instant notifications for new articles
- **Preference personalization**:
  - Subscribers can select preferred categories
  - Option to receive only relevant content
- **Full admin management**:
  - Dedicated interface for manual newsletter sending
  - Open/click statistics tracking
  - Subscriber list management
- **Unsubscribe management**:
  - Dedicated unsubscribe page
  - Streamlined process without infinite redirects
  - User preferences retention
- **Customizable settings**:
  - Configurable sender name and email address
  - Configurable weekly send day
- **Responsive HTML email templates**
- **Automated sending via WP-Cron**

### Advanced Comment Control
- **Global disable** of comments site-wide
- **Selective disable** by content type (posts/pages)
- **Automatic closure** after a set number of days
- **Advanced moderation** with forbidden words filter
- **Editor metabox** to disable comments on specific content
- **Customizer section** for comment control

### Modern User Interface
- **Responsive design** for all devices
- **Customizable visual theme** via Customizer
- **Optimized mobile navigation**
- **Adaptive layout** using Bootstrap
- **Optimized CSS styles** for readability and accessibility
- **Enhanced contrast** for better content clarity
- **Zen reading mode** for distraction-free reading
- **Smart bookmarks system** for saving articles

### Bookmarks System
- **"Save" button** on each article
- **Smart menu management**: "Bookmarks" link shows/hides automatically
- **Bookmarks storage** for logged-in users (database) and visitors (cookies)
- **Dedicated page** listing all bookmarked articles with removal options
- **Intuitive UI** with notifications and animations
- **Dark mode compatibility**

### Advanced Reading Features
- **Distraction-free reading mode**
- **Reading progress bar**
- **Estimated reading time** for each article
- **Reading preferences customization** via Customizer
- **Keyboard shortcut** for zen mode (Alt+Z)
- **Preferences remembered** between sessions

### Technical Optimization
- **Modern JavaScript** (no jQuery dependency)
- **Built-in GDPR compliance**
- **SEO optimization**
- **Performance optimized** for fast loading
- **Modular codebase** for maintainability
- **Compatible with latest WordPress versions**

### Header Customization (NEW)
- **Alternative logo** for dark mode
- **Show/hide search bar** in the header
- **Menu position selection** (left, center, right)
- **Adjustable header height** (slider)

## Installation

1. Download the theme
2. Install it in the `wp-content/themes/` directory of your WordPress installation
3. Activate the theme in the WordPress admin interface
4. Configure theme options via the Customizer and dedicated admin pages

## Configuration

### Newsletter
Go to the "Newsletter" tab in theme settings to configure:
- GDPR consent text for the signup form
- Sender name and email address
- Weekly send day
- Categories available for user preferences

### Comments
Configure comment options via the Customizer in the "Comment Control" section:
- Global enable/disable
- Auto-close settings
- Forbidden words list
- New comment notification options

### Visual Customization
Use the WordPress Customizer to adjust:
- Main theme colors
- Layout options
- Article display options
- Widgets and content areas

## Technical Structure

### Database
- Custom table for newsletter subscribers
- Secure user data storage
- Metadata management for preferences

### Main Files
- `inc/newsletter.php`: Newsletter management
- `inc/rgpd.php`: GDPR compliance features
- `inc/comments-control.php`: Advanced comment control
- `assets/js/newsletter.js`: Newsletter form JS
- `confirm-newsletter.php`: Signup confirmation page
- `unsubscribe-newsletter.php`: Unsubscribe page

## Development

This theme uses modern technologies:
- PHP 7.4+ backend
- Modern JavaScript (ES6+) without jQuery
- Bootstrap for responsive layout
- Custom CSS for UI

### Contributing
Contributions are welcome via pull requests on GitHub.

## License

Copyright 2025 Le Journal des Actus
Distributed under GPL v2 or later.
