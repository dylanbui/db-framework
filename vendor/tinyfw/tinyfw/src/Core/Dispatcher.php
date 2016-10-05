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


class Dispatcher
{
    private $patterns = array(
        ':name'     => '[a-z\-]+',
        ':num'      => '[0-9]+',
        ':slug'     => '[A-Za-z-0-9\-]+',
        ':other'    => '[/]{0,1}[A-Za-z0-9\-\\/\.]+', // => maybe same (:any)
        ':any'      => '.+'
    );

    protected $_controllerNamespace = 'App\Controller';
    protected $_currentRequest;
    protected $_routes = array();
	protected $_preRequest = array();

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

    // -- Su dung bien router tun ben ngoai de co the de dang tach lop router khi can --
    // -- TODO: Dung route tich hop sau, sau nay tach ra sau --
    public function getRoutes()
    {
        return $this->_routes;
    }

    public function setRoutes($routes)
    {
        return $this->_routes = $routes;
    }

	public function addPreRequest($preRequest)
	{
		$this->_preRequest[] = $preRequest;
	}

	public function send()
	{
        // -- Load URL --
        $uri = empty($_GET['_url']) ? ConfigSupport::get('application')['default_uri'] : $_GET['_url'];
        $_GET['_url'] = '/'.str_replace(array('//', '../'), '/', trim($uri, '/'));
        $_GET['_url_params'] = array();
        $_GET['_namespace'] = $this->_controllerNamespace;

		// Load pre config router
        // Loop through the route array looking for wild-cards
        $routes = ConfigSupport::get('routes');
        if(!empty($routes)) // array();
            $this->loadPreRouter($routes);

        // -- Load current request --
        $this->_currentRequest = new Request($_GET['_url'], array(), $_GET['_namespace']);
        if (!empty($_GET['_url_params']))
            $this->_currentRequest->setArgs($_GET['_url_params']);

        // -- Save current Request to Register --
        Container::$_container['oRequest'] = $this->_currentRequest;
//        $this->set('oRequest', $this->_currentRequest);

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
