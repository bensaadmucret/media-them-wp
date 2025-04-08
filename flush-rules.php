<?php
// Script temporaire pour forcer la mise à jour des règles de réécriture

// Charger WordPress
require_once('../../../wp-load.php');

// Inclure la classe newsletter si nécessaire
require_once('inc/newsletter.php');

// Forcer la mise à jour des règles
$result = LeJournalDesActus_Newsletter::force_flush_rewrite_rules();

// Afficher le résultat
if ($result) {
    echo "Les règles de réécriture ont été mises à jour avec succès.";
} else {
    echo "Une erreur s'est produite lors de la mise à jour des règles de réécriture.";
}

// Rediriger vers la page d'accueil après 3 secondes
echo "<script>setTimeout(function() { window.location.href = '/'; }, 3000);</script>";
