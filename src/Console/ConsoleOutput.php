<?php

namespace Greg\ToDo\Console;

class ConsoleOutput
{
    /**
     * @param float $min
     * @param float $max
     * @param float $current
     * @return ProgressOutput
     */
    public function progress(float $min, float $max, float $current): ProgressOutput
    {
        return new ProgressOutput($min, $max, $current);
    }

    /**
     * @param string $text
     * @param bool $displayLabel
     */
    public function success(string $text, bool $displayLabel = true)
    {
        if ($displayLabel) {
            echo "\e[102m\e[30m[ SUCCESS ]";
        }
        echo "\e[49m\e[32m $text\n";
    }

    /**
     * @param string $text
     * @param bool $displayLabel
     */
    public function info(string $text, bool $displayLabel = true)
    {
        if ($displayLabel) {
            echo "\e[46m\e[97m[ INFO ]";
        }
        echo "\e[49m\e[36m $text\n";
    }

    /**
     * @param string $text
     * @param bool $displayLabel
     */
    public function warning(string $text, bool $displayLabel = true)
    {
        if ($displayLabel) {
            echo "\e[43m\e[30m[ WARNING ]";
        }
        echo "\e[49m\e[33m $text\n";
    }

    /**
     * @param string $text
     * @param bool $displayLabel
     */
    public function error(string $text, bool $displayLabel = true)
    {
        if ($displayLabel) {
            echo "\e[41m\e[30m[ ERROR ]";
        }
        echo "\e[49m\e[31m $text\n";
    }

    /**
     * @param string $text
     * @param bool $displayLabel
     */
    public function debug(string $text, bool $displayLabel = true)
    {
        echo "\e[49m\e[33m $text\n";
    }
}