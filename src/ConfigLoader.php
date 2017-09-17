<?php

namespace Greg\ToDo;

use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{
    /** @var string $root */
    private $root;

    /**
     * ConfigLoader constructor.
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @param string $file
     * @return Config
     */
    public function load(string $file){
        $result = $this->parse($file);
        return new Config($result);
    }

    /**
     * @param string $file
     * @return array|mixed
     */
    private function parse(string $file)
    {
        $result = Yaml::parse(file_get_contents($this->root.$file));
        if (!empty($result['imports'])) {

            foreach ($result['imports'] as $import) {
                $importResult = $this->parse($import);
                $result = array_merge($result, $importResult);
            }

            unset($result['imports']);
        }
        return $result;
    }
}