<?php namespace App\Http\Requests;

use Input;
use App\Http\Requests\Request;

/**
 * Request for validating templates
 */
class StoreTemplateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255|unique:templates,name'
        ];

        // On edit add the group ID to the rules
        if ($this->get('id')) {
            $rules['name'] .= ',' . $this->get('id');
        }

        return $rules;
    }
}
