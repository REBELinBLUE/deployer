<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use McCool\LaravelAutoPresenter\BasePresenter;

/**
 * Abstract class to allow the presenter methods to be camel case to match the coding style.
 */
abstract class Presenter extends BasePresenter
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks if the method exists in camel case, else defers to the parent class.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        $method = $this->toCamelCase($key);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return parent::__get($key);
    }

    /**
     * Checks if the method exists in camel case, else defers to the parent class.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset(string $key)
    {
        $method = $this->toCamelCase($key);
        if (method_exists($this, $method)) {
            return true;
        }

        return parent::__isset($key);
    }

    /**
     * Convert a snake_case attribute name to a camelCase method name prefixed with present.
     *
     * @param string $key
     *
     * @return string
     */
    private function toCamelCase($key)
    {
        return Str::camel('present_' . $key);
    }
}
