<?php

namespace REBELinBLUE\Deployer\Console\Commands\Traits;

use Symfony\Component\Console\Question\Question;

/**
 * A trait to add validation to console questions.
 * @todo Split into package
 */
trait AskAndValidate
{
    /**
     * Asks a question and validates the response.
     *
     * @param string   $question
     * @param array    $choices
     * @param callback $validator
     * @param mixed    $default
     * @param bool     $secret
     *
     * @return string
     */
    public function askAndValidate(
        string $question,
        array $choices,
        callable $validator,
        $default = null,
        bool $secret = false
    ): string {
        $question = new Question($question, $default);

        if ($secret) {
            $question->setHidden(true);
        } else {
            $question->setAutocompleterValues($choices);
        }

        $question->setValidator($validator);

        return $this->getOutput()->askQuestion($question);
    }

    /**
     * Asks a question and validates the secret response.
     *
     * @param string   $question
     * @param array    $choices
     * @param callback $validator
     * @param mixed    $default
     *
     * @return string
     */
    public function askSecretAndValidate(string $question, array $choices, callable $validator, $default = null): string
    {
        return $this->askAndValidate($question, $choices, $validator, $default, true);
    }
}
