<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use MicheleAngioni\MultiLanguage\LanguageManager;
use REBELinBLUE\Deployer\Settings;

/**
 * Validate the user settings.
 */
class StoreSettingsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @param  Settings        $settings
     * @param  LanguageManager $languageManager
     * @return array
     */
    public function rules(Settings $settings, LanguageManager $languageManager)
    {
        return [
            'skin'     => 'required|in:' . implode(',', $settings->themes()),
            'scheme'   => 'string|nullable|in:' . implode(',', $settings->schemes()),
            'language' => 'required|in:' . implode(',', $languageManager->getAvailableLanguages()),
        ];
    }
}
