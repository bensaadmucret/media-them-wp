#!/bin/bash

# Filtre pour supprimer la balise de vérification Google des fichiers avant commit
sed '/google-site-verification/d'
