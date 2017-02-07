<?php

namespace REBELinBLUE\Deployer\Validators;

/**
 * Class for validating slack channels.
 */
class ChannelValidator implements ValidatorInterface
{
    /**
     * Validate the the channel name is valid for slack, i.e. starts with # or @.
     *
     * @param array $args
     *
     * @return bool
     */
    public function validate(...$args)
    {
        $value           = $args[1];
        $first_character = substr($value, 0, 1);

        return (($first_character === '#' || $first_character === '@') && strlen($value) > 1);
    }
}
