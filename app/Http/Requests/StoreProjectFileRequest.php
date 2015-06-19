<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating servers.
 */
class StoreProjectFileRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required|max:255',
            'path'       => 'required',
            'content'    => 'required',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
