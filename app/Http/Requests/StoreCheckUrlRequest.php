<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating check urls.
 */
class StoreCheckUrlRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'      => 'required|max:255',
            'url'        => 'required|url',
            'period'     => 'required',
            'is_report'  => 'required|boolean',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
