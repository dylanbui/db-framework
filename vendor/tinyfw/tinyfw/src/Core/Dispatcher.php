<?php

/**
 *
 * @Front Controller class
 *
 * @package Core
 *
 */

namespace TinyFw\Core;

use TinyFw\Support\Config as ConfigSupport;


class Dispatcher extends Container
{
    protected $_defaultControllerNamespace = 'App\Controller';
    protected $_current_request;
	protected $_pre_request = array();

	public function __construct()
    {

    }

    public function getDefaultControllerNamespace()
    {
        return $this->get('defaultControllerNamespace');
    }

    public function setDefaultControllerNamespace($namespace)
    {
        $this->set('defaultControllerNamespace',$namespace);
    }

	public function addPreRequest($pre_request) 
	{
		$this->_pre_request[] = $pre_request;
	}

	public function send()
	{
        // -- Load URL --
        $uri = empty($_GET['_url']) ? ConfigSupport::get('application')['default_uri'] : $_GET['_url'];
        $_GET['_url'] = '/'.str_replace(array('//', '../'), '/', trim($uri, '/'));
        $_GET['_url_params'] = array();
        $_GET['_namespace'] = $this->_defaultControllerNamespace;

		// Load pre config router
        // Loop through the route array looking for wild-cards
        $routes = ConfigSupport::get('routes');
        if(!is_null($routes)) // array();
            $this->loadPreRouter($routes);
		
		$request = NULL;
		foreach ($this->_pre_request as $pre_request)
		{
            $result = $pre_request->run();
			if ($result)
			{
				$request = $result;
				break;
			}
		}

		// -- If pre_request dont return 1 Request --
		if (is_null($request)) 
			$request = $this->getCurrentRequest();

        // -- Save current Request to Register --
        $this->set('oRequest', $request);

        // -- Run Request --
        while ($request instanceof Request) {
            $request = $request->run();
		}

	}
	
	public function getCurrentRequest()
	{
		if (empty($this->_current_request)) {
            $this->_current_request = new Request($_GET['_url'], array(), $_GET['_namespace']);
            if (!empty($_GET['_url_params']))
                $this->_current_request->setArgs($_GET['_url_params']);
        }
		return $this->_current_request;
	}

    private function loadPreRouter($routes)
    {
        $uri = trim($_GET['_url'],'/');

        foreach ($routes as $key => $val)
        {
            // -- Thong tin router la 1 array --
            if (!is_array($val))
                $val = array('path' => $val, 'namespace' => $this->_defaultControllerNamespace);

            $path = $val['path'];

            // Convert wildcards to RegEx
            $key = str_replace(array(':other', ':any', ':num'), array('[/]{0,1}[A-Za-z0-9\-\\/\.]+', '.+', '[0-9]+'), $key);

            // Does the RegEx match?
            if (preg_match('#^'.$key.'$#', $uri, $matches))
            {
                // Do we have a back-reference?
                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $path = preg_replace('#^'.$key.'$#', $path, $uri);
                }

                // -- Save namespace --
                $_GET['_namespace'] = $val['namespace'];
                // -- Save path --
                $_GET['_url'] = $path;
                // -- Remove item first --
                array_shift($matches);
                $_GET['_url_params'] = $matches;

                // -- Lay cai match dau tien --
                return;
            }
        }
    }


} // end of class
