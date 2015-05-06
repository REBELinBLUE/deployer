<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreHeartbeatRequest extends Request
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
            'project_id' => 'required|integer|exists:projects,id'
        ];
    }
}
