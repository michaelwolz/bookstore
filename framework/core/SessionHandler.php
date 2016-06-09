<?php

namespace maw\core;

class SessionHandler
{
    public static function setMAWSession($cName, $cValue)
    {
        $_SESSION[$cName] = $cValue;
    }

    public static function getMAWSession($cName)
    {
        if (!isset($_SESSION[$cName]))
            return null;
        else
            return $_SESSION[$cName];
    }

    public static function destroyMAWSession()
    {
        session_unset();
    }
}