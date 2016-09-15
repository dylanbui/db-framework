<?php

namespace TinyFw\Core;

final class Config
{
	/*
	 * @var array $config_values; 
	 */
	public $config_values = array();

	/**
	 *
	 * @the constructor is set to private so
	 *
	 */
	public function __construct()
	{
		
	}

    public function load($file_config)
    {
        $this->config_values = array_merge(require_once($file_config), $this->config_values) ;
    }

    /**
     *
     * @set undefined vars
     *
     * @param string $key
     *
     * @param mixed $value
     *
     * @return void
     *
     */
    public function set($key, $value)
    {
//        self::$instance->vars[$index] = $value;
        $this->config_values[$key] = $value;
    }

    /**
     *
     * @get variables
     *
     * @param mixed $key
     *
     * @return mixed
     *
     */
    public function get($key)
    {
//        return (isset(self::$instance->vars[$index]) ? self::$instance->vars[$index] : NULL);
        return (isset($this->config_values[$key]) ? $this->config_values[$key] : NULL);
    }

	/**
	 * @get a config option by key
	 *
	 * @access public
	 *
	 * @param string $key:The configuration setting key
	 *
	 * @return string
	 *
	 */
	public function getValue($key)
	{
		return $this->config_values[$key];
	}
}
