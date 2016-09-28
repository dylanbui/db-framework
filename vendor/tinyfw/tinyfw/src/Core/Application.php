<?php
	// define('ENV_PRODUCTION', 'production');
	// define('ENV_STAGING', 'staging');
	// define('ENV_TEST', 'test');
	// define('ENV_DEVELOPMENT', 'development');
	// // Define application environment => 'production'; 'staging'; 'test'; 'development';
	// defined('APPLICATION_ENV') || define('APPLICATION_ENV', 
	// 	(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : ENV_DEVELOPMENT));

namespace TinyFw\Core;

//
//use TinyFw\Core\Input;
//use TinyFw\Core\Config;
//use TinyFw\Core\Registry;
use TinyFw\SessionManager\Session;
use TinyFw\Support\Config as ConfigSupport;

class Application extends Container
{
//    protected $oRegister;
    protected $_defaultControllerNamespace = 'App\Controller';

    protected $appConfig;
    protected $oLoader;
//    protected $oFront;

    function __construct($vars = array())
    {
        parent::__construct($vars);

        $oLoader = Loader::getInstance();

        // register the namspace
        $oLoader->addNamespace('App\Controller', site_path('/app/controllers'));
        $oLoader->addNamespace('App\Lib', site_path('/app/libraries'));
        $oLoader->addNamespace('App\Model', site_path('/app/models'));
        $oLoader->addNamespace('App\Helper', site_path('/app/helpers'));

        $oLoader->register();

        $this->oLoader = $oLoader;

        // -- Define variables --
        $this->initVars();
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    protected function initVars()
    {
        // Create configure object
        $oConfig = new Config();

        // -- Load default config file --
        if(file_exists(site_path('/app/config/config.php')))
            $oConfig->load(site_path('/app/config/config.php'));

        $this->set('oConfig', $oConfig);
        $this->appConfig = $oConfig->get('application');

        $this->set('oSession', function () {
            // SessionManager
            $params = ConfigSupport::get('session');
            $oSession = new Session($params);
            return $oSession;
        });

        $this->set('oInput', function () {
            // Input
            $oInput = new Input();
            return $oInput;
        });

        $this->set('oView', function () {
            // View
            $oView = new View('default/default');
            $oView->setTemplateDir(site_path('/app/views'));
            $oView->setLayoutDir(site_path('/app/layouts'));
            return $oView;
        });

        $this->set('oResponse', function () {
            // Response
            $oResponse = new Response();
            $oResponse->addHeader('Content-Type:text/html; charset=utf-8');
            return $oResponse;
        });

        $this->set('oDispatcher', function () {
            // Response
            $oDispatcher = new Dispatcher();
            return $oDispatcher;
        });

        date_default_timezone_set($this->appConfig['timezone']);

        // register exception handler
        ExceptionHandler::register();

    }

    public function run()
    {

        // -- Send dispatcher --
        $this->oDispatcher->send();

        //-- Level = 0 => get default compression level --
        if ($this->oResponse->getLevel() == 0)
            $this->oResponse->setLevel($this->appConfig['config_compression']);

        if (is_null($this->oResponse->getOutput()))
            $this->oResponse->setOutput($this->oView->getContent());

        //-- TODO : Hook before output content html --
//        $this->oResponse->setOutput(
//            $this->oView->getContent(),
//            $this->oConfig->config_values['application']['config_compression']);

        // -- echo html content --
        $this->oResponse->output();

    }

}
