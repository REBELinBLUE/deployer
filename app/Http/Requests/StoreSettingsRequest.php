<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Validate the user settings.
 */
class StoreSettingsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'skin'   => 'required',
            'scheme' => 'required',
        ];
    }
}
