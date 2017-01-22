<?php

return [

    'label'                 => 'Heartbeats',
    'tab_label'             => 'Health Checks',
    'create'                => 'Add a new heartbeat',
    'edit'                  => 'Edit the heartbeat',
    'url'                   => 'Webhook URL',
    'none'                  => 'The project does not currently have any heartbeats setup',
    'name'                  => 'Name',
    'status'                => 'Status',
    'interval'              => 'Frequency',
    'my_cronjob'            => 'My Cronjob',
    'last_check_in'         => 'Last Check-In',
    'ok'                    => 'Healthy',
    'untested'              => 'Waiting for Heartbeat',
    'missing'               => 'Missing in Action',
    'interval_10'           => '10 minutes',
    'interval_30'           => '30 minutes',
    'interval_60'           => '1 hour',
    'interval_120'          => '2 hours',
    'interval_720'          => '12 hours',
    'interval_1440'         => '1 day',
    'interval_10080'        => '1 week',

    // Notifications
    'missing_message'       => ':job failed to check in at the expected time!',
    'recovered_message'     => ':job has recovered',
    'missing_sms_message'   => ':job for the project ":project" failed to check-in at the expected time! ' .
                               'It last reported in :last',
    'never_sms_message'     => ':job for the project ":project" failed to check-in at the expected time! ' .
                               'It has never reported in',
    'recovered_sms_message' => ':job for the project ":project" has recovered',
    'missing_subject'       => 'The heartbeat failed to check-in',
    'recovered_subject'     => 'The heartbeat has recovered',

];
