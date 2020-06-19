<?php

namespace App;

class Auth
{
    protected static $user;

    public static function authenticate($required = false)
    {
        if (!self::$user) {
            $userId = Session::get("user_id");
            if (!$userId) {
                if (!$required) return;
                header("Location: /login");
                exit;
            }

            $user = Db::fetchRow("SELECT * FROM user WHERE id = ?", [ $userId ]);
            if (!$user) {
                if (!$required) return;
                header("Location: /login");
                exit;
            }

            unset($user['password'], $user['salt']);

            self::$user = $user;
        }

        return self::$user;
    }

    public static function login($username, $password)
    {
        $user = Db::fetchRow("SELECT * FROM user WHERE username = ?", [ $username ]);
        if (!$username) {
            return null;
        }

        $passHash = hash('sha256', $user['salt'].$password);
        if ($passHash != $user['password']) {
            return null;
        }

        unset($user['password'], $user['salt']);

        Session::set('user_id', $user['id']);
        self::$user = $user;

        return $user;
    }

    public static function logout()
    {
        Session::unset('user_id');
        self::$user = null;
    }
}
