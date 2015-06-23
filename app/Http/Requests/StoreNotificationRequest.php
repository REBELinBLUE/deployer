<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Factory;

/**
 * Request for validating notifications.
 */
class StoreNotificationRequest extends Request
{
    /**
     * Overwrite the parent constructor to define a new validator.
     *
     * @param  Factory $factory
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(Factory $factory)
    {
        $factory->extend(
            'channel',
            function ($attribute, $value, $parameters) {
                $first_character = substr($value, 0, 1);

                return (($first_character === '#' || $first_character === '@') && strlen($value) > 1);
            }
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|max:255',
            'channel'      => 'required|max:255|channel',
            'webhook'      => 'required|regex:/^https:\/\/hooks.slack.com' .
                              '\/services\/[a-z0-9]+\/[a-z0-9]+\/[a-z0-9]+$/i',
            'failure_only' => 'boolean',
            'project_id'   => 'required|integer|exists:projects,id',
        ];
    }
}
