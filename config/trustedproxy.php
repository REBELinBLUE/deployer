<?php

return [

    /*
     * Set trusted proxy IP addresses.
     *
     * Both IPv4 and IPv6 addresses are
     * supported, along with CIDR notation.
     *
     * The "*" character is syntactic sugar
     * within TrustedProxy to trust any proxy;
     * a requirement when you cannot know the address
     * of your proxy (e.g. if using Rackspace balancers).
     */
    'proxies' => env('TRUSTED_PROXIES', '*') === '*' ?: explode(',', env('TRUSTED_PROXIES')),

];
