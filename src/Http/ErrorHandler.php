<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Config;

class ErrorHandler
{
    /** @var Config $config */
    public $config;
    /** @var string $exception */
    public $exception;
    /** @var $callback */
    public $callback;
    /** @var boolean $strictMode */
    private $strictMode = true;

    /**
     * ErrorHandler constructor.
     * @param Config $config
     * @param string $exception
     * @param callable|string $callback
     */
    public function __construct(Config $config, string $exception, $callback)
    {
        $this->exception = $exception;
        $this->callback = $callback;
    }

    /**
     * @param \Exception $exception
     * @return bool
     */
    public function isMatch(\Exception $exception)
    {
        if ($this->strictMode === false) {
            return $exception instanceof $this->exception;
        }

        return get_class($exception) === $this->exception;
    }

    /**
     * @param \Twig_Environment $twig
     * @param \Exception $exception
     * @return mixed
     */
    public function run(\Twig_Environment $twig, \Exception $exception)
    {
        $callbackHandler = new CallbackHandler($this->config);
        return $callbackHandler->run($this->callback, array($twig, $exception));
    }

    /**
     * @return bool
     */
    public function isStrictMode(): bool
    {
        return $this->strictMode;
    }

    /**
     * @param bool $strictMode
     */
    public function setStrictMode(bool $strictMode)
    {
        $this->strictMode = $strictMode;
    }

}