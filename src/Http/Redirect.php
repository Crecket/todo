<?php

namespace Greg\ToDo\Http;

class Redirect
{
    /**
     * @var string
     */
    private $url;

    /**
     * Redirect constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     *
     */
    public function redirect()
    {
        header("Location: ".$this->url);
    }
}