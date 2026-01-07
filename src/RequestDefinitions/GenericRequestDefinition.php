<?php

namespace Istoy\RequestDefinitions;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Validation;
use Illuminate\Support\Facades\Validator;

abstract class GenericRequestDefinition
{
    /**
     * Options set on the request.
     *
     * These are usually applied from the code creating the definition instance,
     * as the definition itself has an options() method for defaults.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Validate the request data, and throw a ValidationException if one or more
     * validation rules fail.
     *
     * @throws ValidationException
     * @return static
     */
    public function validate(): static
    {
        $validator = $this->validator();

        if ($validator->fails()) {
            throw new ValidationException($this, $validator);
        }

        return $this;
    }

    /**
     * JSON data to set on the request body.
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html#json
     * @return array|null
     */
    public function payload(): ?array
    {
        return null;
    }

    public function validator(): Validation\Validator
    {
        return Validator::make(
            $this->validationData(),
            $this->rules(),
            $this->validationMessages(),
            $this->validationAttributes(),
        );
    }

    /**
     * Return the message bag of validation errors. If validation hasn't been
     * performed yet, it will be.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->validator()->errors();
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    protected function validationMessages(): array
    {
        return [];
    }

    /**
     * Custom validation attribute values
     *
     * @return array
     */
    protected function validationAttributes(): array
    {
        return [];
    }

    /**
     * Collect all information needed to pass validation for this definition.
     *
     * @return array
     */
    protected function validationData(): array
    {
        return $this->payload() ?? [];
    }

    /**
     * Has the form_params/json/query data passed all validation rules?
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validator()->passes();
    }

    /**
     * Add one or more Guzzle options.
     *
     * @param array $options
     * @return static
     */
    public function addOptions(array $options): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Collect all options for the request.
     *
     * @return array
     */
    protected function allOptions(): array
    {
        return array_merge(
            $this->options(),
            $this->options,
        );
    }

    /**
     * Is this request definition describing a call to a 3rd party API / service?
     *
     * @return bool
     */
    public function isExternal(): bool
    {
        return true;
    }

    /**
     * Description of the request definition. By default this is the FQN for the
     * class on the bottom of the concrete class.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return null;
    }

    /**
     * When invoking a concrete instance of this class, build and send a PendingRequest
     * based on the request definition.
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->send();
    }

    /**
     * Customize options for the request.
     *
     * @return array
     */
    abstract public function options(): array;

    /**
     * Validation rules to apply to the request.
     *
     * @link https://laravel.com/docs/validation#available-validation-rules
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Send the built request defined by this class.
     *
     * @return mixed
     */
    abstract public function send();

    /**
     * Headers to apply to the request.
     *
     * @return array
     */
    abstract public function headers(): array;

    /**
     * How many times can the *exact same* request be issued to an external service
     * within the decay window?
     *
     * @see decayWindowSeconds()
     * @return int
     */
    public function maxExecutionsWithinDecayWindow(): int
    {
        return 2;
    }

    /**
     * The decay window (in seconds) within which the max number of executions of
     * this request definition is allowed.
     *
     * For example:
     *
     *  - decay window: 15
     *  - max executions: 2
     *
     * This means within any 15 second period of time, the *same* request cannot
     * be issued more than 2 times.
     *
     * @see maxExecutionsWithinDecayWindow()
     * @return int
     */
    public function decayWindowSeconds(): int
    {
        return 15;
    }

    /**
     * Generate a unique identifier for the request.
     *
     * This is used to track and prevent identical, duplicate calls from being
     * processed in quick succession.
     *
     * @return string
     */
    abstract public function uniqueID(): string;
}

