<?php
	// define('ENV_PRODUCTION', 'production');
	// define('ENV_STAGING', 'staging');
	// define('ENV_TEST', 'test');
	// define('ENV_DEVELOPMENT', 'development');
	// // Define application environment => 'production'; 'staging'; 'test'; 'development';
	// defined('APPLICATION_ENV') || define('APPLICATION_ENV', 
	// 	(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : ENV_DEVELOPMENT));
		
	// define the site path __SITE_PATH : c:\xampp\htdocs\adv_mvc
	define ('__SITE_PATH', realpath(dirname(dirname(__FILE__))));
	// __SITE_URL : /adv_mvc/
    $tmp = str_replace('public_html/', '', $_SERVER['SCRIPT_NAME']);
    define ('__SITE_URL', str_replace(basename($tmp), '', $tmp));
// 	define ('__SITE_URL', str_replace('/'.basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));

	// __BASE_URL : /adv_mvc/
// 	define ('__BASE_URL', __SITE_URL);
 	// Co thu muc public_html 	
 	define ('__PUBLIC_HTML', __SITE_URL);
 	
 	// ---- Khong Thay Doi ---- // 	
 	define ('__ASSET_URL', __PUBLIC_HTML.'assets');
    define ('__COMPONENT_URL', __ASSET_URL.'/plugins');
 	define ('__IMAGE_URL', __ASSET_URL.'/images');
 	define ('__CSS_URL', __ASSET_URL.'/css');
 	define ('__JS_URL', __ASSET_URL.'/js');
 	
	// the application directory path 
	define ('__APP_PATH', __SITE_PATH.'/app');
	define ('__VIEW_PATH', __APP_PATH.'/views');	
	define ('__LAYOUT_PATH', __APP_PATH.'/layouts');
	// define ('__HELPER_PATH', __APP_PATH.'/helpers');
//	define ('__CONFIG_PATH', __APP_PATH.'/config');

	define ('__UPLOAD_DATA_PATH', __SITE_PATH.'/public_html/data/upload/');
	define ('__UPLOAD_DATA_URL', __PUBLIC_HTML . 'data/upload/');
	
	define ('__DATA_PATH', __SITE_PATH . '/public_html/data/');
	define ('__DATA_URL', __PUBLIC_HTML . 'data/');

//	define ('__CONTROLLER_NAMESPACE', 'App\Controller');

 //    // Load config files. Global config file
	// require __SITE_PATH.'/app/libraries/Core/Psr4Autoloader.php';
	// // instantiate the loader
 // 	$loader = new Psr4Autoloader();
 // 	// register the base directories for the namespace prefix
 	
 // 	$loader->addNamespace(__CONTROLLER_NAMESPACE, __APP_PATH.'/controllers'); 	
 // 	$loader->addNamespace('App\Lib', __SITE_PATH.'/app/libraries'); 	
 // 	$loader->addNamespace('App\Model', __SITE_PATH.'/app/models');
 // 	$loader->addNamespace('App\Helper', __SITE_PATH.'/app/helpers');
 // 	// register the autoloader
 // 	$loader->register();

//    echo "<pre>";
//    print_r($_SERVER);
//    echo "</pre>";
//
//    $const = get_defined_constants(true);
//    echo "<pre>";
//    print_r($const['user']);
//    echo "</pre>";
//    exit();

 //    // Create configure object
 //    $config = \App\Lib\Core\Config::getInstance();

	// define('APPLICATION_ENV', $config->config_values['application']['application_env']); 
	// // set the timezone
	// date_default_timezone_set($config->config_values['application']['timezone']);	

 //    require __SITE_PATH . '/app/config/constants.php';   

	// /*** set error handler level to E_WARNING ***/
	// // error_reporting($config->config_values['application']['error_reporting']);
	// // set_error_handler('_exception_handler', $config->config_values['application']['error_reporting']);
	// \App\Lib\Core\ErrorHandler::register();

//	$registry = null;
//	$config = null;
//
//	require __SITE_PATH . '/admin/startup.php';
//
//	$oBenchmark = new \App\Lib\Core\Benchmark();
//	$oBenchmark->mark('code_start');
//
// 	/*** a new registry object ***/
// 	$registry = new \App\Lib\Core\Registry();
//
//	// Loader
//	$registry->oLoader = $loader;
//
// 	// Session
// 	$oSession = new \App\Lib\Session();
// 	$registry->oSession = $oSession;
//
// 	// Input
// 	$oInput = new \App\Lib\Input();
// 	$registry->oInput = $oInput;
//
//	// Response
//	$response = new \App\Lib\Core\Response();
//	$response->addHeader('Content-Type: text/html; charset=utf-8');
//	$registry->oResponse = $response;
//
//	// Config
//	$registry->oConfig = $config;
//
//	// Parameter
//	$view = new \App\Lib\Core\View();
//    $view->setTemplateDir(__VIEW_PATH);
//    $view->setLayoutDir(__LAYOUT_PATH);
//	$registry->oView = $view;
//
//	// Initialize the FrontController
//	$front = \App\Lib\Core\FrontController::getInstance();
//	$front->setRegistry($registry);
	
	/*
		// Cau hinh cho cac action nay chay dau tien 
	$front->addPreRequest(new Request('run/first/action')); 
	$front->addPreRequest(new Request('run/second/action'));
	*/

//    $front->addPreRequest(new \App\Lib\Core\Request('member-manager/member/get-login-info'));
//
//	$front->dispatch();
//
//	// Output
//	$response->output();
//
//	if($config->config_values['application']['show_benchmark'])
//	{
//		$oBenchmark->mark('code_end');
//		echo "<br>".$oBenchmark->elapsed_time('code_start', 'code_end');
//	}

require_once __SITE_PATH.'/vendor/autoload.php';
require_once __SITE_PATH.'/app/config/constants.php';

class Application extends \TinyFw\Core\Application
{
    function __construct()
    {
        parent::__construct();
    }

//    protected function createCache()
//    {
//        $config_cache = $this->oConfig->config_values['cache'];
//        $config_cache['cache_path'] = __SITE_PATH.'/public_html/cache/';
//        $cache = new \TinyFw\Cache($config_cache);
//        return $cache;
//    }
//
//    function run()
//    {
//        $this->oCache = $this->createCache();
//
//        $this->oFront->addPreRequest(new \TinyFw\Core\Request('member-manager/member/get-login-info'));
//
//        return parent::run();
//    }
}


//class A {
//
//    public static function __callStatic($name, $params) {
//        var_dump($name);
//
//        // TODO call the private function from class B here
//
//        //call_user_func_array('self::' . $name, $params); //infinite loop
//
//        //forward_static_call_array(array(self, $name), $params); // loops too
//
//        $method = new ReflectionMethod(get_called_class(), $name);
//        $method->setAccessible(true);
//        $method->invokeArgs(null, $params);
//
//    }
//
//}
//
//
//class MethodTest extends A
//{
//    /*
//     * @var object $instance
//    */
//    private static $instance = null;
//
//
//    /**
//     * Configure some default session setting and then start the session.
//     * @param   array
//     */
//    public function __construct($params = array())
//    {
//        if (is_null(self::$instance)) {
//            self::$instance = $this;
//        }
//    }
//
////    public function __call($name, $arguments)
////    {
////        // Note: value of $name is case sensitive.
////        echo "Calling object method '$name' "
////            . implode(', ', $arguments). "\n";
////    }
//
//    /**  As of PHP 5.3.0  */
////    public static function __callStatic($name, $arguments)
////    {
//////        // Note: value of $name is case sensitive.
//////        echo "Calling static method '$name' "
//////            . implode(', ', $arguments). "\n";
//////
//////        $name = 'call_'.$name;
////////        self::$instance->saveData($arguments[0]);
//////        self::$instance->{$name}($arguments);
//////        call_user_func_array()
//////        var_dump($name);
////
////        // TODO call the private function from class B here
////
////        //call_user_func_array('self::' . $name, $params); //infinite loop
////
////        //forward_static_call_array(array(self, $name), $params); // loops too
////
////        $method = new ReflectionMethod(get_called_class(), $name);
////        $method->setAccessible(true);
////        $method->invokeArgs(null, $arguments);
////
////    }
//
//    public function saveData($data)
//    {
//        echo "Luu du lieu tai day -- " . $data;
//    }
//
//    public function runTest($data)
//    {
//        echo "Luu du lieu tai day -- " . $data;
//    }
//}
//
//$obj = new MethodTest;
//$obj->runTest('in object context');
//echo "<br>";
//
//MethodTest::saveData('in static context');  // As of PHP 5.3.0
//
//echo "<br>";

$app = new Application();
$app->run();//->dispatch();

