<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Exceptions\Http\InvalidHeaderException;
use Greg\ToDo\Exceptions\Http\InvalidHeaderStringException;

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
        $this->headers = $this->parseHeaders($headers);
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
     * @throws InvalidHeaderException
     */
    private function parseHeaders()
    {
        $headers = [];
        foreach ($this->headers as $header) {
            if ($header instanceof Header) {
                $headers[] = $header;
                continue;
            }

            if (is_string($header)) {
                $headerParts = explode(":", $header);

                if (count($headerParts) === 0) {
                    throw new InvalidHeaderException();
                }

                $headerType = $headerParts[0];
                unset($headerParts[0]);

                // implode back into a string in case value contained more : characters
                $headerValue = implode(":", $headerParts);

                $headers[] = new Header($headerType, $headerValue);
                continue;
            }

            if (is_array($header)) {
                $headers[] = new Header($header[0], $header[1]);
                continue;
            }

            throw new InvalidHeaderException();
        }
        return $headers;
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