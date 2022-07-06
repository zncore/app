<?php

namespace ZnCore\App\Helpers;

class EnvServerHelper
{

    public static function isEqualUri(string $name)
    {
        return trim($_SERVER['REQUEST_URI'], '/') == trim($name, '/');
    }

    public static function isPostMethod(): bool
    {
        global $_SERVER;
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function isOptionsMethod(): bool
    {
        global $_SERVER;
        return $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
    }

    public static function isContainsSegmentUri(string $name)
    {
        $isMatch = preg_match('/(\/' . $name . ')($|\/|\?)/', $_SERVER['REQUEST_URI'], $matches);
        return $isMatch ? $matches[1] : null;
    }

    /*public static function redirectToShash(string $name) {
        if($_SERVER['REQUEST_URI'] === '/' . $name) {
            $r = new RedirectResponse('/' . $name . '/');
            $r->send();
            exit;
            //dd($_SERVER['REQUEST_URI'], $name);
        }
    }*/

    public static function fixUri(string $name)
    {
        if (ltrim($_SERVER['REQUEST_URI'], '/') === $name) {
            $_SERVER['REQUEST_URI'] .= '/';
        }
        $_SERVER['SCRIPT_NAME'] = "/$name/index.php";
        $_SERVER['PHP_SELF'] = "/$name/index.php";
    }
}
