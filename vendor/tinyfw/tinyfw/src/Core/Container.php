<?php

namespace TinyFw\Core;

class Container {

    /*
     * @var object $instance
    */
    protected static $_container = array();

    /**
     *
     * @the constructor is set to private so
     * @param array $array
     */
    public function __construct($array = array())
    {
        if (is_array($array) && !empty($array))
            self::$_container = array_merge($array, self::$_container);
    }

    /**
     *
     * @set undefined vars
     *
     * @param string $index
     *
     * @param mixed $value
     *
     * @return void
     *
     */
    public function __set($index, $value)
    {
        self::$_container[$index] = $value;
    }

    /**
     *
     * @get variables
     *
     * @param mixed $index
     *
     * @return mixed
     *
     */
    public function __get($index)
    {
        return (isset(self::$_container[$index]) ? self::$_container[$index] : NULL);
    }

    public function has($index)
    {
        return isset(self::$_container[$index]);
    }
}

?>
