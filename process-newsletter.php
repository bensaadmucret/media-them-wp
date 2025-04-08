<?php
/**
 * Fichier de traitement direct des souscriptions à la newsletter
 */

// Charger WordPress
require_once('../../../wp-load.php');

// Inclure la classe newsletter si nécessaire
require_once('inc/newsletter.php');

// Traiter la souscription
LeJournalDesActus_Newsletter::process_direct_subscription();
