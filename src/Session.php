<?php

namespace App;

/**
 * Session wrapper
 *
 * @author Wubbo Bos <wubbo@wubbobos.nl>
 */
class Session
{
    /** @var bool $started Flag to see if the PHP session is started */
    protected static $started = false;

    /**
     * Ensures that the session is started
     */
    protected static function ensureSession()
    {
        if (!self::$started) {
            session_start();
            self::$started = true;
        }
    }

    /**
     * Returns a session variable with $name
     *
     * @param string $name
     * @return mixed The value, or NULL if the value is not set
     */
    public static function get(string $name)
    {
        self::ensureSession();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Sets a session variable with $name
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set(string $name, $value)
    {
        self::ensureSession();
        $_SESSION[$name] = $value;
    }

    /**
     * Unsets a session variable with $name
     *
     * @param string $name
     */
    public static function unset($name)
    {
        self::ensureSession();
        unset($_SESSION[$name]);
    }
}
