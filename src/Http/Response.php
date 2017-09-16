<?php

namespace Greg\ToDo\Http;

class Response
{
    /** @var string $body */
    private $body;
    /** @var int $code */
    private $code;
    /** @var Header[] $headers */
    private $headers;

    /**
     * Response constructor.
     * @param string $body
     * @param int $code
     * @param array $headers
     */
    public function __construct(string $body, int $code = 200, array $headers = array())
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function output()
    {
        http_response_code($this->code);
        foreach ($this->headers as $header) {
            if (!$header instanceof Header) {
                throw new \Exception("Header is not an instance of ".Header::class);
            }
            $header->output();
        }
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }

    /**
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param Header[] $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}