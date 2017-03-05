<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Generic Request class.
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param Guard $auth
     *
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return $auth->check();
    }
}
