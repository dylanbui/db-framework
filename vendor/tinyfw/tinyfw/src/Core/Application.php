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

    public function get($key)
    {
        return parent::__get($key);
    }

    public function set($key, $value)
    {
        parent::__set($key, $value);
    }

    protected function initVars()
    {

        $this->set('oConfig', function () {
            // Create configure object
            $oConfig = new Config();

            // -- Load default config file --
            if(file_exists(site_path('/app/config/config.php')))
                $oConfig->load(site_path('/app/config/config.php'));

            return $oConfig;
        });

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

        $this->appConfig = ConfigSupport::get('application');

        date_default_timezone_set($this->appConfig['timezone']);

        // register exception handler
        ExceptionHandler::register();

    }

    // -- 2 dong nay tam dong de kiem tra --
//    public function __get($key)
//    {
//        return parent::__get($key);
//    }
//
//    public function __set($key, $value)
//    {
//        parent::__set($key, $value);
//    }

    protected function createFrontController()
    {
        // Initialize the FrontController
        $oFront = Core\FrontController::getInstance();
//        $oFront->setRegistry($this->oRegister);
        $oFront->setDefaultControllerNamespace('App\Controller'); // Default : 'App\Controller'
        return $oFront;
    }


    public function run()
    {
        date_default_timezone_set($this->oConfig->config_values['application']['timezone']);

        // register exception handler
        Core\ExceptionHandler::register();

        $this->oSession = $this->createSession();

        $this->oInput = $this->createInput();

        $this->oView = $this->createView();

        $this->oResponse = $this->createResponse();

        $this->oFront->dispatch();

        //-- Level = 0 => get default compression level --
        if ($this->oResponse->getLevel() == 0)
            $this->oResponse->setLevel($this->oConfig->config_values['application']['config_compression']);

        if (is_null($this->oResponse->getOutput()))
            $this->oResponse->setOutput($this->oView->getContent());

        //-- TODO : Hook before output content html --
//        $this->oResponse->setOutput(
//            $this->oView->getContent(),
//            $this->oConfig->config_values['application']['config_compression']);

        // -- echo html content --
        $this->oResponse->output();

//        return $this->oFront;


        // Initialize the FrontController
//        $oFront = Core\FrontController::getInstance();
//        $oFront->setRegistry($this->oRegister);
//        $oFront->setDefaultControllerNamespace('App\Controller'); // Default : 'App\Controller'
//
//        $this->oRegister->oFront = $oFront;

//        $this->loadRegister();
//
//        $this->loadConfig();
//
//        $this->loadSession();
//
//        $this->loadInput();
//
//        $this->loadView();
//
//        $this->loadCache();
//
//        $this->loadResponse();
//
//        // Initialize the FrontController
//        $this->front = \TinyFw\Core\FrontController::getInstance();
//        $this->front->setRegistry($this->registry);
//        $this->front->setDefaultControllerNamespace('App\Controller'); // Default : 'App\Controller'

        /*
            // Cau hinh cho cac action nay chay dau tien
        $front->addPreRequest(new Request('run/first/action'));
        $front->addPreRequest(new Request('run/second/action'));
        */

//        $this->oFront->addPreRequest(new \TinyFw\Core\Request('member-manager/member/get-login-info'));

//        echo "<pre>";
//        print_r(site_path('app'));
//        echo "</pre>";
//        echo "<pre>";
//        print_r(site_path('views'));
//        echo "</pre>";
//        echo "<pre>";
//        print_r(site_path('layout'));
//        echo "</pre>";
//        exit();
//
//        tinyfw_url();





//        $this->oFront->dispatch();

//        $p = \TinyFw\Core\Config::getInstance();
//
//        $in = new \TinyFw\Input();
//
//        echo "<pre>";
//        print_r($in->get('aaaaa','gia tri mac dinh'));
//        echo "</pre>";
//
//        echo "<pre>";
//        print_r(tinyfw_now_to_mysql());
//        echo "</pre>";
//
//        exit();

        // -- Chi de tam --
//        if($this->registry->oConfig->config_values['application']['show_benchmark'])
//        {
//            $oBenchmark->mark('code_end');
//            echo "<br>".$oBenchmark->elapsed_time('code_start', 'code_end');
//        }

    }

    public function getDefaultControllerNamespace()
    {
        return $this->_defaultControllerNamespace;
    }

    public function setDefaultControllerNamespace($namespace)
    {
        $this->_defaultControllerNamespace = $namespace;
    }

}
