<?php

return [

    'manage'            => 'Gestion des projets',
    'warning'           => 'Le projet n\'a pas pu être enregistré, merci de vérifier le formulaire ci-dessous.',
    'none'              => 'Aucun projet n\'a encore été ajouté',
    'name'              => 'Nom',
    'awesome'           => 'Ma super application',
    'group'             => 'Groupe',
    'repository'        => 'Dépôt',
    'repository_url'    => 'URL du dépôt',
    'builds'            => 'Versions à conserver',
    'build_options'     => 'Options',
    'project_details'   => 'Informations',
    'branch'            => 'Branche par défaut',
    'image'             => 'Image de statut CI',
    'ci_image'          => 'Si vous utilisez un serveur d\'intégration continue qui fournit une image de statut du projet,' .
                           'vous pouvez saisir son adresse ici afin de l\'afficher sur la page du projet.',
    'latest'            => 'Dernier déploiement',
    'create'            => 'Ajouter un projet',
    'edit'              => 'Éditer un projet',
    'url'               => 'URL',
    'details'           => 'Détails du projet',
    'deployments'       => 'Déploiements',
    'today'             => 'Aujourd\'hui',
    'last_week'         => 'La semaine dernière',
    'latest_duration'   => 'Dernière durée',
    'health'            => 'Bilan de santé',
    'build_status'      => 'Statut de la version',
    'app_status'        => 'Statut de l\'application',
    'heartbeats_status' => 'Statut du Heartbeat',
    'view_ssh_key'      => 'Voir la clé SSH',
    'private_ssh_key'   => 'Clé SSH privée',
    'public_ssh_key'    => 'Clé SSH publique',
    'ssh_key'           => 'Clé SSH',
    'ssh_key_info'      => 'Si vous souhaitez utiliser une clé privée spécifique, vous pouvez la coller ici. Elle ne doit pas comporter de mot de passe.',
    'ssh_key_example'   => 'Une clé SSH sera automatiquement générée si vous n\'en saisissez pas ici, c\'est l\'option que nous vous recommandons.',
    'deploy_project'    => 'Déployer le projet',
    'deploy'            => 'Déployer',
    'redeploy'          => 'Redéployer',
    'server_keys'       => 'Cette clé doit être ajoutée sur le serveur dans le fichier
                             <code>~/.ssh/authorized_keys</code> ' .
                           'de chaque utilisateur qui doit exécuter des commandes.',
    'git_keys'          => 'La clé doit également être ajoutée dans les clés de déploiments
                            de votre hébergeur Git, ' .
                           'à moins bien sûr que vous n\'utilisiez pas un dépôt privé derrière une authentification.',
    'finished'          => 'Terminé',
    'pending'           => 'En attente',
    'deploying'         => 'En cours de déploiement',
    'failed'            => 'En échec',
    'not_deployed'      => 'Jamais déployé',
    'options'           => 'Options',
    'change_branch'     => 'Autoriser le déploiement d\'autres branches ?',
    'include_dev'       => 'Installer les paquets Composer ?',
    'insecure'          => 'Your Deployer installation is not running over a secure connection, it is recommended ' .
                           'that you let Deployer generate an SSH key rather than supply one yourself so that the ' .
                           'private key is not transmitted over an insecure connection.',
    'users'             => 'Utilisateurs',

];
