<?php

namespace App;

/**
 * Class to load configuration per item
 *
 * @author Wubbo Bos
 */
class Config
{
    /** @var array $registered Loaded configuration sets */
    protected static $registered = [];

    /** @var array $data The configuration data */
    protected $data;

    /**
     * Merges two arrays
     *
     * @param array $array1
     * @param array $array2
     * @return array The merged array
     */
    protected static function mergeDeep(array $array1, array $array2)
    {
        $result = $array1;
        foreach ($array2 as $key => $value) {
            if (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                $result[$key] = self::mergeDeep($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns the configureation for a specific item
     *
     * @param string $key The name (key) of the item
     * @return Config The configuration
     */
    public static function get($key)
    {
        if (!isset(self::$registered[$key])) {
            $files = glob(dirname(__DIR__) . "/config/{$key}.*.config.php");
            $config = [];
            foreach ($files as $file) {
                $part = include $file;
                if (is_array($part)) {
                    $config = self::mergeDeep($config, $part);
                }
            }

            self::$registered[$key] = new Config($config);
        }

        return self::$registered[$key];
    }

    /**
     * Constructor
     *
     * @param array $data The raw configuration data
     */
    public function __construct(array $data = [])
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new Config($value);
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Returns the value for a specific key
     *
     * @param string $name The key
     * @return mixed The value
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
}
