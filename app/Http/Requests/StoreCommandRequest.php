<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

use Input;

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
            'name'       => 'required',
            'user'       => 'required',
            'script'     => 'required',
            'step'       => 'required',
            'project_id' => 'required|integer'
        ];

        // On edit we don't require the step or the project_id
        // // FIXME: This is wrong
        if (Input::get('command_id')) {
            unset($rules['step']);
            unset($rules['project_id']);
        }

        return $rules;
    }
}
