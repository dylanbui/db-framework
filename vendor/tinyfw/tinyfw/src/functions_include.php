<?php

// Don't redefine the functions if included multiple times.
//if (!function_exists('GuzzleHttp\Psr7\str')) {
//    require __DIR__ . '/functions.php';
//}

//if (!function_exists('redirect')) {
//    function redirect($uri = '', $method = 'location', $http_response_code = 302)
//    {
//        if (!preg_match('#^https?://#i', $uri)) {
//            $uri = site_url($uri);
//        }
//
//        switch ($method) {
//            case 'refresh'  :
//                header("Refresh:0;url=" . $uri);
//                break;
//            default         :
//                header("Location: " . $uri, TRUE, $http_response_code);
//                break;
//        }
//        exit;
//    }
//}
//
//if (!function_exists('current_site_url')) {
//    function current_site_url($uri = '')
//    {
//        $pageURL = 'http';
//        $pageURL .= "://";
//        if ($_SERVER["SERVER_PORT"] != "80") {
//            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
//        } else {
//            $pageURL .= $_SERVER["SERVER_NAME"];
//        }
//        return $pageURL . site_url($uri);
//    }
//}
//
//if (!function_exists('site_url')) {
//    function site_url($uri = '')
//    {
//        static $_site_url = null;
//        if(is_null($_site_url))
//        {
//            $tmp = str_replace('public_html/', '', $_SERVER['SCRIPT_NAME']);
//            $_site_url = str_replace(basename($tmp), '', $tmp);
//        }
//        return $_site_url.ltrim($uri, '/');
//    }
//}

if (!function_exists('public_html')) {
    function public_html($uri_file = '')
    {
        return site_url($uri_file);
    }
}

if (!function_exists('site_path')) {
    function site_path($dir = '')
    {
        static $_site_path = null;
        if(is_null($_site_path))
        {
            if (preg_match('#^(.+)/vendor/(.+)$#', __FILE__, $matches)) {
                $_site_path = $matches[1];
            }
        }
        return $_site_path.'/'.ltrim($dir, '/');
    }
}


function tinyfw_url()
{
    if (preg_match('#^(.+)/vendor/(.+)$#', __FILE__, $matches)) {

        echo "<pre>";
        print_r($matches);
        echo "</pre>";
        exit();

    }

//    define ('__SITE_URL', str_replace(basename($tmp), '', $tmp));
}

