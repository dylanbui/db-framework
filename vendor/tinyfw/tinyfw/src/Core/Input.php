<?php

namespace TinyFw\Core;

class Input 
{	
	public $_get = array();
	public $_post = array();
	public $_request = array();
	public $_files = array();
	
	public function __construct() 
	{
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_REQUEST = $this->clean($_REQUEST);
		$_FILES = $this->clean($_FILES);
	
		$this->_get = &$_GET;
		$this->_post = &$_POST;
		$this->_request = &$_REQUEST;
		$this->_files = &$_FILES;
		
		$this->_cookie = &$_COOKIE;		
		$this->_server = &$_SERVER;
	}
	
	public function clean($data) 
	{
		if (is_array($data)) 
		{
			foreach ($data as $key => $value) 
			{
				unset($data[$key]);
				$data[$this->clean($key)] = $this->clean($value);
			}
		} else {
// 			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
			$data = $this->xss_clean($data);
		}
	
		return $data;
	}

    public function isAjax()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    public function isPost()
    {
        return !empty($_POST);
    }

    public function request()
    {
        return $this->_request;
    }

	public function post($name,$default_value = NULL)
	{
		if (!isset($this->_post[$name]) || $this->_post[$name] == "")
		{
			return $default_value;
		}
		return $this->_post[$name];
	}

	public function get($name,$default_value = NULL)
	{
		if (!isset($this->_get[$name]) || $this->_get[$name] == "")
		{
			return $default_value;
		}
		return $this->_get[$name];
	}

    public function file($name)
    {
        return (isset($this->_files[$name])) ? $this->_files[$name] : null;
    }

	/*
	 * XSS filter
	*
	* This was built from numerous sources
	* (thanks all, sorry I didn't track to credit you)
	*
	* It was tested against *most* exploits here: http://ha.ckers.org/xss.html
	* WARNING: Some weren't tested!!!
	* Those include the Actionscript and SSI samples, or any newer than Jan 2011
	*
	*
	* TO-DO: compare to SymphonyCMS filter:
	* https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
	* (Symphony's is probably faster than my hack)
	*/
	
	private function xss_clean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		// we are done...
		return $data;
	}
	
	
}

?>