<?php namespace App\Http\Requests;

use Input;
use App\Http\Requests\Request;

/**
 * Request for validating commands
 */
class StoreCommandRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'       => 'required|max:255',
            'user'       => 'required|max:255',
            'script'     => 'required|max:255',
            'step'       => 'required', // FIXME: There are a set of values
            'project_id' => 'required|integer'
        ];

        // On edit we don't require the step or the project_id
        if ($this->get('id')) {
            unset($rules['step']);
            unset($rules['project_id']);
        }

        return $rules;
    }
}
