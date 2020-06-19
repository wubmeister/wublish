<?php

namespace App;

/**
 * Authentication class
 *
 * @author Wubbo Bos <wubbo@wubbobos.nl>
 */
class Auth
{
    /** @var array $user The currently logged in user */
    protected static $user;

    /**
     * Returns the currently logged in user, if any
     *
     * I no user is logged in, depending on the $required parameter, either NULL
     * is returned or a redirect will take place to the login screen
     *
     * @param bool $required TRUE if authentication is required. If set to TRUE
     *      and no user is logged in, a redirect will take place to the login screen
     * @return array|null The user
     */
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

    /**
     * Tries to login with the given credentials
     *
     * @param string $username
     * @param string $password
     * @return array|null If successful, the user is returned, else NULL is returned
     */
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

    /**
     * Logs out the current user
     */
    public static function logout()
    {
        Session::unset('user_id');
        self::$user = null;
    }
}
