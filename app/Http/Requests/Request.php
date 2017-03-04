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
     * @var Guard
     */
    protected $auth;

    /**
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->auth->check();
    }
}
