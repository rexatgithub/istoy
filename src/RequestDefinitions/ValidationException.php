<?php

namespace Istoy\RequestDefinitions;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class ValidationException extends LaravelValidationException
{
    protected $requestDefinition;

    public function __construct($requestDefinition, $validator)
    {
        $this->requestDefinition = $requestDefinition;
        parent::__construct($validator);
    }

    public function getRequestDefinition()
    {
        return $this->requestDefinition;
    }
}

