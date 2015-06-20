<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Factory;

/**
 * Request for validating servers.
 */
class StoreServerRequest extends Request
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
            'host',
            function ($attribute, $value, $parameters) {
                if (filter_var($value, FILTER_VALIDATE_IP)) {
                    return true;
                }

                if (filter_var(gethostbyname($value), FILTER_VALIDATE_IP)) {
                    return true;
                }

                return false;
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
            'user'         => 'required|max:255',
            'ip_address'   => 'required|host',
            'path'         => 'required',
            'add_commands' => 'boolean',
            'project_id'   => 'required|integer|exists:projects,id',
        ];
    }
}
