<?php

return [

    'label'             => 'Variables d\'environnement',
    'create'            => 'Ajouter une variable',
    'edit'              => 'Éditer la variable',
    'name'              => 'Variable',
    'value'             => 'Valeur',
    'warning'           => 'Cette variable n\'a pas pu être enregistrée, merci de vérifier le formulaire ci-dessous.',
    'description'       => 'Vous pourriez avoir besoin de variables d\'environnement pendant le processus de déploiement mais vous ne souhaitez pas
														les ajouter dans le <code>~/.bashrc</code> sur le serveur.',
    'example'           => 'Par exemple, vous pourriez vouloir définir <code>COMPOSER_PROCESS_TIMEOUT = 3600</code> pour permettre au Composer de s\'exécuter ' .
                           'plus longtemps, ou bien <code>SYMFONY_ENV = local</code> si vous déployez un projet Symfony.',

];
