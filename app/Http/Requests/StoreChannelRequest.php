<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating channels.
 */
class StoreChannelRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = array_merge([
            'name'                       => 'required|max:255',
            'project_id'                 => 'required|integer|exists:projects,id',
            'type'                       => 'required|in:slack,twilio,mail,custom',
            'on_deployment_success'      => 'boolean',
            'on_deployment_failure'      => 'boolean',
            'on_link_down'               => 'boolean',
            'on_link_still_down'         => 'boolean',
            'on_link_recovered'          => 'boolean',
            'on_heartbeat_missing'       => 'boolean',
            'on_heartbeat_still_missing' => 'boolean',
            'on_heartbeat_recovered'     => 'boolean',
        ], $this->configRules());

        if ($this->route('notification')) {
            unset($rules['project_id']);
        }

        return $rules;
    }

    /**
     * Gets the input which are allowed in the request based on the type.
     *
     * @return array
     */
    public function configOnly(): array
    {
        return $this->only(array_keys($this->configRules()));
    }

    /**
     * Validation rules specific to slack.
     *
     * @return array
     */
    private function slackRules(): array
    {
        return [
            'channel' => 'required|max:255|channel',
            'webhook' => 'required|url',
            'icon'    => 'string|nullable|regex:/^:(.*):$/',
        ];
    }

    /**
     * Validation rules specific to email.
     *
     * @return array
     */
    private function mailRules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * Validation rules specific to twilio.
     *
     * @return array
     */
    private function twilioRules(): array
    {
        return [
            'telephone' => 'required|regex:/^\+([0-9]*)$/',
        ];
    }

    /**
     * Validation rules specific to custom channels.
     *
     * @return array
     */
    private function customRules(): array
    {
        return [
            'url' => 'required|url',
        ];
    }

    /**
     * Gets the additional rules based on the type from the request.
     *
     * @return array
     */
    private function configRules(): array
    {
        switch ($this->get('type')) {
            case 'slack':
                return $this->slackRules();
            case 'twilio':
                return $this->twilioRules();
            case 'mail':
                return $this->mailRules();
            case 'custom':
            default:
                return $this->customRules();
        }
    }
}
