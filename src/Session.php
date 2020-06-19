<?php

namespace App;

class Session
{
    protected static $started = false;

    protected static function ensureSession()
    {
        if (!self::$started) {
            session_start();
            self::$started = true;
        }
    }

    public static function get($name)
    {
        self::ensureSession();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public static function set($name, $value)
    {
        self::ensureSession();
        $_SESSION[$name] = $value;
    }

    public static function unset($name)
    {
        self::ensureSession();
        unset($_SESSION[$name]);
    }
}
