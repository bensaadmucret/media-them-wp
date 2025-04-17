# Le Journal des Actus - Thème WordPress

Un thème WordPress personnalisé pour "Le Journal des Actus" avec un système de newsletter intégré et conforme RGPD, conçu pour offrir une expérience utilisateur optimale et une gestion de contenu efficace.

## Fonctionnalités principales

### Système de Newsletter avancé
- **Double opt-in** : Conformité RGPD avec confirmation par email
- **Texte de consentement RGPD personnalisable** via le panneau d'administration
- **Système hybride de diffusion** :
  - Digest hebdomadaire automatique des nouveaux articles
  - Notifications immédiates optionnelles pour les nouveaux articles
- **Personnalisation des préférences** :
  - Sélection de catégories préférées par les abonnés
  - Option pour recevoir uniquement les contenus pertinents
- **Administration complète** :
  - Interface dédiée pour l'envoi manuel de newsletters
  - Suivi des statistiques d'ouverture et de clic
  - Gestion des listes d'abonnés
- **Gestion des désabonnements** :
  - Page dédiée pour le désabonnement
  - Processus simplifié sans redirection infinie
  - Conservation des préférences utilisateur
- **Paramètres personnalisables** :
  - Nom de l'expéditeur configurable
  - Adresse email de l'expéditeur configurable
  - Jour d'envoi hebdomadaire configurable
- **Templates d'emails HTML responsive** pour une présentation optimale sur tous les appareils
- **Automatisation via WP-Cron** pour l'envoi programmé des newsletters

### Contrôle avancé des commentaires
- **Désactivation globale** des commentaires sur l'ensemble du site
- **Désactivation sélective** par type de contenu (articles/pages)
- **Fermeture automatique** des commentaires après un nombre défini de jours
- **Modération avancée** avec filtrage des mots interdits
- **Métabox dans l'éditeur** pour désactiver les commentaires sur des contenus spécifiques
- **Configuration via Customizer** dans une section dédiée "Contrôle des commentaires"

### Interface utilisateur moderne
- **Design responsive** adapté à tous les appareils
- **Thème visuel personnalisable** via le Customizer
- **Navigation mobile optimisée** pour une expérience utilisateur fluide
- **Mise en page adaptative** avec Bootstrap
- **Styles CSS optimisés** pour une meilleure lisibilité et accessibilité
- **Contraste amélioré** pour une meilleure lisibilité des contenus
- **Mode lecture zen** permettant une lecture sans distraction des articles
- **Système de favoris intelligent** permettant aux utilisateurs de sauvegarder des articles pour une lecture ultérieure

### Système de favoris (Bookmarks)
- **Bouton "Enregistrer"** sur chaque article pour l'ajouter aux favoris
- **Gestion intelligente du menu** : le lien "Favoris" apparaît/disparaît automatiquement selon la présence d'articles enregistrés
- **Stockage des favoris** pour les utilisateurs connectés (base de données) et les visiteurs (cookies)
- **Page dédiée** listant tous les articles favoris avec options de suppression
- **Interface intuitive** avec notifications et animations lors de l'ajout/suppression
- **Compatibilité avec le mode sombre** pour une expérience utilisateur cohérente

### Fonctionnalités de lecture avancées
- **Mode lecture sans distraction** avec basculement facile
- **Barre de progression de lecture** indiquant la position dans l'article
- **Estimation du temps de lecture** pour chaque article
- **Personnalisation des préférences de lecture** via le Customizer
- **Raccourcis clavier** pour activer/désactiver le mode lecture zen (Alt+Z)
- **Mémorisation des préférences** entre les sessions

### Personnalisation de l'en-tête (NOUVEAU)
- **Logo alternatif** pour le mode sombre
- **Affichage/masquage de la barre de recherche** dans l'en-tête
- **Position du menu** (gauche, centre, droite)
- **Hauteur du header ajustable** (slider)

### Optimisation technique
- **JavaScript moderne** sans dépendance à jQuery
- **Conformité RGPD** intégrée à tous les niveaux
- **Optimisation SEO** pour un meilleur référencement
- **Performance optimisée** pour des temps de chargement rapides
- **Code modulaire** pour une maintenance facilitée
- **Compatibilité avec les dernières versions de WordPress**

## Installation

1. Téléchargez le thème
2. Installez-le dans le dossier `wp-content/themes/` de votre installation WordPress
3. Activez le thème depuis l'interface d'administration WordPress
4. Configurez les options du thème via le Customizer et les pages d'administration dédiées

## Configuration

### Newsletter
Accédez à l'onglet "Newsletter" dans les paramètres du thème pour configurer :
- Le texte de consentement RGPD affiché dans le formulaire d'inscription
- Le nom et l'adresse email de l'expéditeur des newsletters
- Le jour d'envoi hebdomadaire de la newsletter
- Les catégories disponibles pour la sélection des préférences

### Commentaires
Configurez les options de commentaires via le Customizer de WordPress dans la section "Contrôle des commentaires" :
- Activation/désactivation globale
- Paramètres de fermeture automatique
- Liste de mots interdits pour la modération
- Options de notification pour les nouveaux commentaires

### Personnalisation visuelle
Utilisez le Customizer WordPress pour ajuster :
- Les couleurs principales du thème
- La disposition des éléments
- Les options d'affichage des articles
- Les widgets et zones de contenu

## Structure technique

### Base de données
- Table personnalisée pour les abonnés à la newsletter
- Stockage sécurisé des données utilisateur
- Gestion des métadonnées pour les préférences

### Fichiers principaux
- `inc/newsletter.php` : Gestion de la newsletter
- `inc/rgpd.php` : Fonctionnalités de conformité RGPD
- `inc/comments-control.php` : Contrôle avancé des commentaires
- `assets/js/newsletter.js` : Script JavaScript pour la gestion du formulaire
- `confirm-newsletter.php` : Page de confirmation d'inscription
- `unsubscribe-newsletter.php` : Page de désabonnement

## Développement

Ce thème utilise des technologies modernes :
- PHP 7.4+ pour le backend
- JavaScript moderne (ES6+) sans dépendance à jQuery
- Bootstrap pour la mise en page responsive
- CSS personnalisé pour l'interface utilisateur

### Contribution
Les contributions sont les bienvenues via pull requests sur GitHub.

## Licence

Copyright 2025 Le Journal des Actus
Distribué sous licence GPL v2 ou ultérieure.
