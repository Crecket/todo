<?php

namespace Greg\ToDo\Http;

class Request
{
    /** @var string $url */
    private $url;
    /** @var string $method */
    private $method;
    /** @var array $parameters */
    private $parameters;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        if (!empty($_POST['_method'])) {
            switch (strtoupper($_POST['_method'])) {
                case 'PUT':
                    $this->method = "PUT";
                    break;
                case 'DELETE':
                    $this->method = "DELETE";
                    break;
                default:
                    $this->method = "POST";
                    break;
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function getParameter(string $key): string
    {
        return $this->parameters[$key] ?? null;
    }

    /**
     * @param string|null $key
     * @return null
     */
    public function getQuery(string $key = null)
    {
        if (is_null($key)) {
            return $_GET;
        }
        return $_GET[$key] ?? null;
    }

    /**
     * @param string|null $key
     * @return null
     */
    public function getPost(string $key = null)
    {
        if (is_null($key)) {
            return $_POST;
        }
        return $_POST[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}