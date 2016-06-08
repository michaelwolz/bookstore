<?php

namespace maw\core;

class CookieHandler
{
    public static function setMAWCookie($cName, $cValue)
    {
        setcookie($cName, $cValue, time() + (86400 * 30), "/");
        $_COOKIE[$cName] = $cValue;
    }

    public static function getMAWCookie($cName)
    {
        if (!isset($_COOKIE[$cName]))
            return null;
        else
            return $_COOKIE[$cName];
    }

    public static function removeMAWCookie($cName)
    {
        if (isset($_COOKIE[$cName])) {
            unset($_COOKIE[$cName]);
            setcookie($cName, '', time() - 3600, '/'); // empty value and old timestamp
        }
    }
}