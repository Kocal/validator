<?php

namespace Kocal\Validator;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class Validator
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var Factory
     */
    private $validatorFactory;

    /**
     * @var array
     */
    private $rules;

    /**
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Validator constructor.
     *
     * @param array       $rules
     * @param string      $locale
     * @param string|null $fallback
     */
    public function __construct(array $rules = [], $locale = 'fr', $fallback = null)
    {
        $this->rules = $rules;
        $this->locale = $locale;

        $loader = new FileLoader(new Filesystem(), __DIR__ . '/lang');
        $translator = new Translator($loader, $this->locale);

        if ($fallback !== null) {
            $translator->setFallback($fallback);
        }

        $this->validatorFactory = new Factory($translator, new Container());
    }

    /**
     * Register a custom validator extension.
     *
     * @param  string          $rule
     * @param  \Closure|string $extension
     * @param  string          $message
     *
     * @return void
     */
    public function extend($rule, $extension, $message = null)
    {
        $this->validatorFactory->extend($rule, $extension, $message);
    }

    /**
     * @param array $data
     * @param array $messages
     * @param array $customAttributes
     */
    public function validate(array $data, array $messages = [], array $customAttributes = [])
    {
        $this->validator = $this->validatorFactory->make($data, $this->rules, $messages, $customAttributes);
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->validator->passes();
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
