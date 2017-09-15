<?php

return [

    'name'              => 'Deployer',
    'signout'           => 'Sign Out',
    'toggle_nav'        => 'Toggle Navigation',
    'dashboard'         => 'Dashboard',
    'admin'             => 'Administration',
    'projects'          => 'Projects',
    'templates'         => 'Templates',
    'groups'            => 'Groups',
    'users'             => 'Users',
    'servers'           => 'Servers',
    'created'           => 'Created',
    'edit'              => 'Edit',
    'confirm'           => 'Confirm',
    'not_applicable'    => 'N/A',
    'date'              => 'Date',
    'status'            => 'Status',
    'details'           => 'Details',
    'delete'            => 'Delete',
    'save'              => 'Save',
    'close'             => 'Close',
    'never'             => 'Never',
    'none'              => 'None',
    'yes'               => 'Yes',
    'no'                => 'No',
    'warning'           => 'WARNING',
    'socket_error'      => 'Server error',
    'socket_error_info' => 'A connection could not be established to the socket at ' .
                           '<strong>' . config('deployer.socket_url') . '</strong>. This is required for reporting ' .
                           'the status on running deployments. Please reload, if the issue continues please ' .
                           'contact the system administrator',
    'update_available'  => 'An update is available!',
    'outdated'          => 'You are running an out of date release :current, there is an updated release ' .
                           '<a href=":link" rel="noreferrer">:latest</a> available!',

];
