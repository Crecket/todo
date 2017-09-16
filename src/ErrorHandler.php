<?php

namespace Greg\ToDo;

class ErrorHandler
{
    /** @var string $exception */
    public $exception;
    /** @var callable $callback */
    public $callback;
    /** @var boolean $strictMode */
    private $strictMode = true;

    /**
     * ErrorHandler constructor.
     * @param string $exception
     * @param callable $callback
     */
    public function __construct(string $exception, callable $callback)
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
    public function run(\Twig_Environment $twig, $exception)
    {
        return call_user_func(
            $this->callback,
            $twig,
            $exception
        );
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