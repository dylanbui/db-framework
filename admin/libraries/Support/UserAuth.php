<?php

/**
 * Created by PhpStorm.
 * User: dylanbui
 * Date: 9/12/16
 * Time: 11:22 PM
 */

namespace Admin\Lib\Support;

use TinyFw\Support\SupportInterface;

class UserAuth extends SupportInterface
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    protected static function getSupportClass()
    {
        return "oUserAuth";
    }

}