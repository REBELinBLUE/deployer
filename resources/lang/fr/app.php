<?php

return [

    'name'              => 'Deployer',
    'signout'           => 'Se déconnecter',
    'toggle_nav'        => 'Afficher/Masquer la barre de navigation',
    'dashboard'         => 'Tableau de bord',
    'admin'             => 'Administration',
    'projects'          => 'Projets',
    'templates'         => 'Gabarits',
    'groups'            => 'Groupes',
    'users'             => 'Utilisateurs',
    'created'           => 'Créé(e)',
    'edit'              => 'Editer',
    'confirm'           => 'Confirmer',
    'not_applicable'    => 'N/A',
    'date'              => 'Date',
    'status'            => 'Statut',
    'details'           => 'Détails',
    'delete'            => 'Supprimer',
    'save'              => 'Enregistrer',
    'close'             => 'Fermer',
    'never'             => 'Jamais',
    'none'              => 'Aucun',
    'yes'               => 'Oui',
    'no'                => 'Non',
    'warning'           => 'ATTENTION',
    'socket_error'      => 'Erreur de l\'application',
    'socket_error_info' => 'La connexion à la socket n\'a pas pu être établie à l\'adresse ' .
                           '<strong>' . config('deployer.socket_url') . '</strong>.
                           Cela est nécessaire pour récupérer automatiquement le statut des déploiements.
                            Si le problème subsiste, merci de contacter votre administrateur système.',
//    'not_down'          => 'You must switch to maintenance mode before running this command, this will ensure that ' .
//                           'no new deployments are started',
//    'switch_down'       => 'Switch to maintenance mode now? The app will switch back to live mode once cleanup ' .
//                           'is finished',
    'update_available'  => 'Une mise à jour est disponible !',
    'outdated'          => 'Votre application tournant sur la version :version peut être mise à jour.
													 Voici la dernière mise à jour disponible : ' .
                           '<a href=":link" rel="noreferrer">:latest</a>',

];
