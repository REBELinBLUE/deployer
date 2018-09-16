<?php

return [

    'label'                 => 'URL Tests',
    'create'                => 'Add a new URL',
    'edit'                  => 'Edit the URL',
    'none'                  => 'You can add URLs which should be requested periodically and after a deployment has ' .
                               'finished.',
    'title'                 => 'Title',
    'titleTip'              => 'Admin Panel',
    'url'                   => 'URL',
    'frequency'             => 'Frequency',
    'length'                => ':time minutes',
    'warning'               => 'The link could not be saved, please check the form below.',
    'last_status'           => 'Status',
    'successful'            => 'Online',
    'failed'                => 'Offline',
    'untested'              => 'Unknown',
    'last_seen'             => 'Last Seen',
    'log'                   => 'View Failure Log',
    'log_title'             => 'Failure Log',
    'string_to_search'      => 'String to search on the page',
    'avoid_special_chars'   => 'Avoid using special characters as they might be escaped during the process which ' .
                               'could lead to a failed test',

    // Notifications
    'down_message'          => ':link appears to be down',
    'recovered_message'     => ':link is back online',
    'down_sms_message'      => ':link for the project ":project" appears to be down! It was last seen :last',
    'never_sms_message'     => ':link for the project ":project" appears to be down! It has never been online',
    'recovered_sms_message' => ':link for the project ":project" is back online',
    'down_subject'          => 'The URL is down',
    'recovered_subject'     => 'The URL is back online',

];
