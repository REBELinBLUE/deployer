<?php

namespace REBELinBLUE\Deployer\Console\Commands\Traits;

use Symfony\Component\Console\Question\Question;

/**
 * A trait to add validation to console questions.
 **/
trait AskAndValidate
{
    /**
     * Asks a question and validates the response.
     *
     * @param  string   $question  The question
     * @param  array    $choices   Autocomplete options
     * @param  function $validator The callback function
     * @param  mixed    $default   The default value
     * @return string
     */
    public function askAndValidate($question, array $choices, $validator, $default = null)
    {
        $question = new Question($question, $default);

        if (count($choices)) {
            $question->setAutocompleterValues($choices);
        }

        $question->setValidator($validator);

        return $this->output->askQuestion($question);
    }
}
