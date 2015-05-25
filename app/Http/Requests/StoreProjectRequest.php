<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating projects
 * TODO: Define a better validator for the repository, should ensure it is a valid URL and has .git at the end
 */
class StoreProjectRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'           => 'required|max:255',
            'repository'     => 'required',
            'branch'         => 'required|max:255',
            'group_id'       => 'required|integer|exists:groups,id',
            'builds_to_keep' => 'required|integer|min:1|max:20',
            'url'            => 'url',
            'build_url'      => 'url'
        ];
    }
}
