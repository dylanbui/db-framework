<?php

/**
 *
 * @Front Controller class
 *
 * @package Core
 *
 */

namespace TinyFw\Core;

class Dispatcher
{
    private $patterns = array(
        ':name'     => '[a-z\-]+',
        ':num'      => '[0-9]+',
        ':slug'     => '[A-Za-z-0-9\-]+',
        ':other'    => '[/]{0,1}[A-Za-z0-9\-\\/\.]+', // => maybe same (:any)
        ':any'      => '.+'
    );

    protected $_defaultUri = 'index/index';
    protected $_controllerNamespace = 'App\Controller';
    protected $_currentRequest;
    protected $_routes = array();
	protected $_preRequest = array();
    protected $_postRequest = array();

	public function __construct($controllerNamespace)
    {
        $this->_controllerNamespace = $controllerNamespace;
    }

    public function getCurrentRequest()
    {
        return $this->_currentRequest;
    }

    public function getControllerNamespace()
    {
        return $this->_controllerNamespace;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function setRoutes($routes)
    {
        return $this->_routes = $routes;
    }

    public function getDefaultUri()
    {
        return $this->_defaultUri;
    }

    public function setDefaultUri($uri)
    {
        return $this->_defaultUri = $uri;
    }

	public function addPreRequest(Request $preRequest)
	{
		$this->_preRequest[] = $preRequest;
	}

    public function addPostRequest(Request $postRequest)
    {
        $this->_postRequest[] = $postRequest;
    }

	public function send()
	{
        // -- Load URL --
        $uri = empty($_GET['_url']) ? $this->_defaultUri : $_GET['_url'];
        $_GET['_url'] = '/'.str_replace(array('//', '../'), '/', trim($uri, '/'));
        $_GET['_url_params'] = array();
        $_GET['_namespace'] = $this->_controllerNamespace;

		// Load pre config router
        // Loop through the route array looking for wild-cards
        if(!empty($this->_routes)) // array();
            $this->loadPreRouter($this->_routes);

        // -- Load current request --
        $this->_currentRequest = new Request($_GET['_url'], array(), $_GET['_namespace']);
        if (!empty($_GET['_url_params']))
            $this->_currentRequest->setArgs($_GET['_url_params']);

        // -- Save current Request to Register --
        Container::$_container['oRequest'] = $this->_currentRequest;

        // -- Loop pre request --
		$request = $this->_currentRequest;
		foreach ($this->_preRequest as $preRequest)
		{
            $result = $preRequest->run();
			if ($result)
			{
				$request = $result;
				break;
			}
		}

        // -- Run Request --
        while ($request instanceof Request) {
            $request = $request->run();
		}

        // -- Loop post request --
        foreach ($this->_postRequest as $postRequest)
        {
            $postRequest->run();
        }
	}

    private function loadPreRouter($routes)
    {
        $uri = trim($_GET['_url'],'/');

        foreach ($routes as $key => $val)
        {
            // -- Thong tin router la 1 array --
            if (!is_array($val))
                $val = array('path' => $val, 'namespace' => $this->_controllerNamespace);

            $path = $val['path'];

            // Convert wildcards to RegEx
            $key = str_replace(array_keys($this->patterns), array_values($this->patterns), $key);

            // Does the RegEx match?
            if (preg_match('#^'.$key.'$#', $uri, $matches))
            {
                // Do we have a back-reference?
                if (strpos($path, '$') !== FALSE AND strpos($key, '(') !== FALSE)
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
