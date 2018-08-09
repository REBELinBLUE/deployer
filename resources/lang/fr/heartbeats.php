<?php

return [

    'label'                 => 'Battements de cœur',
    'tab_label'             => 'Bilans de santé',
    'create'                => 'Ajouter une tâche',
    'edit'                  => 'Éditer la tâche planifiée',
    'url'                   => 'URL du Webhook',
    'none'                  => 'Le projet n\'a encore aucune tâche configurée',
    'name'                  => 'Nom',
    'status'                => 'Statut',
    'warning'               => 'La tâche n\'a pas pu être enregistrée, merci de vérifier le formulaire ci-dessous.',
    'interval'              => 'Fréquence',
    'my_cronjob'            => 'Ma tâche planifiée',
    'last_check_in'         => 'Dernière vérification',
    'ok'                    => 'Sain',
    'untested'              => 'En attente d\'un battement de cœur',
    'missing'               => 'Manquante',
    'interval_10'           => '10 minutes',
    'interval_30'           => '30 minutes',
    'interval_60'           => '1 heure',
    'interval_120'          => '2 heures',
    'interval_720'          => '12 heures',
    'interval_1440'         => '1 jour',
    'interval_10080'        => '1 semaine',

    // Notifications
    'missing_message'       => 'La tâche :job n\'a pas été reçue à temps',
    'recovered_message'     => 'La tâche :job est revenue à la vie',
    'missing_sms_message'   => 'La tâche :job du projet ":project" n\'a pas été reçue à temps ' .
                               'Elle a répondu pour la dernière fois :last',
    'never_sms_message'     => 'La tâche :job du projet ":project" n\'a pas été reçue à temps. ' .
                               'Elle n\'a jamais fonctionnée !',
    'recovered_sms_message' => 'La tâche :job du projet ":project" est revenue à la vie',
    'missing_subject'       => 'Le battement de cœur n\'a pas été reçu à temps',
    'recovered_subject'     => 'Le battement de cœur est de nouveau opérationnel',

];
