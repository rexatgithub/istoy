<?php

namespace Istoy\RequestDefinitions;

use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use ReflectionClass;

/**
 * A request definition defines the HTTP method, headers, URL, query string,
 * body, and more for an HTTP PendingRequest instance.
 */
abstract class RequestDefinition extends GenericRequestDefinition
{
    /**
     * HTTP 'GET' method.
     *
     * @var string
     */
    public const HTTP_GET = 'GET';

    /**
     * HTTP 'POST' method.
     *
     * @var string
     */
    public const HTTP_POST = 'POST';

    /**
     * HTTP 'PUT' method.
     *
     * @var string
     */
    public const HTTP_PUT = 'PUT';

    /**
     * HTTP 'OPTIONS' method.
     *
     * @var string
     */
    public const HTTP_OPTIONS = 'OPTIONS';

    /**
     * HTTP 'PATCH' method.
     *
     * @var string
     */
    public const HTTP_PATCH = 'PATCH';

    /**
     * HTTP 'DELETE' method.
     *
     * @var string
     */
    public const HTTP_DELETE = 'DELETE';

    /**
     * Query string params to apply to the request.
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html#query
     * @return array
     */
    public function queryParams(): array
    {
        return [];
    }

    /**
     * Headers to apply to the request.
     *
     * @return array
     */
    public function headers(): array
    {
        return [];
    }


    /**
     * Request resolver
     *
     * @var Closure
     */
    protected Closure $requestResolver;

    /**
     * Combine the form params, json data, and query string params into a single
     * array to be used to validate the request.
     *
     * @return array
     */
    protected function validationData(): array
    {
        return array_merge(parent::validationData(), $this->queryParams());
    }

    /**
     * Attempt to build a PendingRequest instance based on this class definition.
     *
     * Guzzle request options can be passed to merge with those set on the definition.
     *
     * This method will throw a ValidationException if the data for the request
     * does not pass all validation rules set on the definition.
     *
     * @throws ValidationException
     *
     * @param array $options
     * @return PendingRequest
     */
    public function buildRequest(array $options = []): PendingRequest
    {
        $request = $this->validate()
            ->createNewRequest()
            ->withHeaders($this->headers())
            ->withOptions($this->allOptions())
            ->withOptions($options)
            ->withOptions([
                'query' => $this->queryParams(),
            ]);

        if (
            config('app.debug', false) &&
            !(new ReflectionClass($this))->isAnonymous()
        ) {
            $request->withHeaders(['Request-Definition' => static::class]);
        }

        return $request;
    }

    /**
     * Use Guzzle's 'sink' ability to dump the response content into storage.
     *
     * @param string $filepath
     * @return self
     */
    public function saveToFile(string $filepath): self
    {
        $this->addOptions(['sink' => $filepath]);

        return $this;
    }

    /**
     * Manually customize options for the request.
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html
     * @return array
     */
    public function options(): array
    {
        return [];
    }

    /**
     * Convert the definition into a PendingRequest instance.
     *
     * @return Response|PromiseInterface
     */
    public function send()
    {
        $request = $this->buildRequest()->timeout(30);
        $method = strtolower($this->method());

        return $request->{$method}($this->url(), $this->payload());
    }

    /**
     * Set a function that returns a base PendingRequest instance the definition
     * should use as the starting point when building new requests.
     *
     * @param Closure $resolver
     * @return self
     */
    public function setRequestResolver(Closure $resolver): self
    {
        $this->requestResolver = $resolver;

        return $this;
    }

    /**
     * Set the request resolver to once that expects/interprets the body as
     * multipart/form-data.
     *
     * Guzzle will properly set the Content-Type header with correct boundary for
     * the request.
     *
     * @return self
     */
    public function asMultipart(): self
    {
        return $this->setRequestResolver(fn() => Http::asMultipart());
    }

    /**
     * Set the request resolver to once that expects/interprets the body as
     * application/x-www-form-urlencoded form data.
     *
     * Guzzle will properly set the Content-Type header for the request.
     *
     * @return self
     */
    public function asForm(): self
    {
        return $this->setRequestResolver(fn() => Http::asForm());
    }

    /**
     * Fetch the closure that defines the origin of the PendingRequest the definition
     * should use to build up a request.
     *
     * @return Closure
     */
    public function getRequestResolver(): Closure
    {
        return $this->requestResolver ?? fn() => Http::asJson();
    }

    /**
     * Create a new PendingRequest instance by calling through to the currently
     * set request resolver.
     *
     * @return PendingRequest
     */
    public function createNewRequest(): PendingRequest
    {
        return call_user_func($this->getRequestResolver());
    }

    /**
     * Generate a unique identifier for the request.
     *
     * This is used to track and prevent identical, duplicate calls from being
     * processed in quick succession.
     *
     * @return string
     */
    public function uniqueID(): string
    {
        $parts = [
            get_class($this),
            $this->method(),
            $this->url(),
            json_encode($this->payload()),
            json_encode($this->headers()),
            json_encode($this->queryParams()),
        ];

        return md5(implode(':', $parts));
    }

    /**
     * The HTTP method to use when sending the request.
     *
     * @return string
     */
    abstract public function method(): string;

    /**
     * The URL (or endpoint if the base URL has been previously set on the request),
     * to send the request to.
     *
     * @return string
     */
    abstract public function url(): string;
}

