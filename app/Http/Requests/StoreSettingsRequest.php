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
        // TODO: Maybe define these somewhere so they aren't duplicated
        return [
            'skin'     => 'required|in:yellow,yellow-light,red,red-light,green,' .
                          'green-light,purple,purple-light,blue,blue-light',
            'scheme'   => 'required|in:default,afterglow,monokai,dawn,solarized-dark,solarized-light',
            'language' => 'required|in:en,zh'
        ];
    }
}
