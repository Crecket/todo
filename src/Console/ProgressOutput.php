<?php

namespace Greg\ToDo\Console;

class ProgressOutput
{
    /** @var float $min */
    private $min;
    /** @var float $max */
    private $max;
    /** @var float $current */
    private $current;

    /**
     * ProgressOutput constructor.
     * @param float $min
     * @param float $max
     * @param float $current
     */
    public function __construct(float $min, float $max, float $current)
    {
        $this->min = $min;
        $this->max = $max;
        $this->current = $current;
    }

    public function render()
    {
        $progressCharacters = 100;

        $step = ($this->max - $this->min) / ($progressCharacters / 5);
        $percent = round($this->current / $step);

        $stepLabel = ($this->max - $this->min) / $progressCharacters;
        $percentLabel = round($this->current / $stepLabel);

        echo "[";
        echo str_pad(
            str_pad("", $percent, "="),
            ($progressCharacters / 5),
            "-");
        echo "] ($percentLabel%)\r";
    }

    /**
     * @return float
     */
    public function getMin(): float
    {
        return $this->min;
    }

    /**
     * @param float $min
     */
    public function setMin(float $min)
    {
        $this->min = $min;
    }

    /**
     * @return float
     */
    public function getMax(): float
    {
        return $this->max;
    }

    /**
     * @param float $max
     */
    public function setMax(float $max)
    {
        $this->max = $max;
    }

    /**
     * @return float
     */
    public function getCurrent(): float
    {
        return $this->current;
    }

    /**
     * @param float $current
     */
    public function setCurrent(float $current)
    {
        $this->current = $current;
    }

}