<?php
	// define('ENV_PRODUCTION', 'production');
	// define('ENV_STAGING', 'staging');
	// define('ENV_TEST', 'test');
	// define('ENV_DEVELOPMENT', 'development');
	// // Define application environment => 'production'; 'staging'; 'test'; 'development';
	// defined('APPLICATION_ENV') || define('APPLICATION_ENV', 
	// 	(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : ENV_DEVELOPMENT));

	// define the site path __SITE_PATH : c:\xampp\htdocs\adv_mvc
	define ('__SITE_PATH', realpath(dirname(dirname(dirname(__FILE__)))));
	// __SITE_URL : /adv_mvc/
    $tmp = str_replace('public_html/', '', $_SERVER['SCRIPT_NAME']);
    define ('__SITE_URL', str_replace(basename($tmp), '', $tmp));

 	// Co thu muc public_html
    define ('__PUBLIC_HTML', str_replace('//', '/', dirname(__SITE_URL).'/'));
 	
 	// ---- Khong Thay Doi ---- // 	
 	define ('__ASSET_URL', __PUBLIC_HTML.'assets');
    define ('__COMPONENT_URL', __ASSET_URL.'/plugins');
 	define ('__IMAGE_URL', __ASSET_URL.'/images');
 	define ('__CSS_URL', __ASSET_URL.'/css');
 	define ('__JS_URL', __ASSET_URL.'/js');

    define ('__TEMPLATE_URL', __PUBLIC_HTML.'adminlte/');
 	
	// the application directory path 
	define ('__APP_PATH', __SITE_PATH.'/admin');
	define ('__VIEW_PATH', __APP_PATH.'/views');	
	define ('__LAYOUT_PATH', __APP_PATH.'/layouts');
	// define ('__HELPER_PATH', __APP_PATH.'/helpers');
//	define ('__CONFIG_PATH', __APP_PATH.'/config');

	define ('__UPLOAD_DATA_PATH', __SITE_PATH.'/public_html/data/upload/');
	define ('__UPLOAD_DATA_URL', __PUBLIC_HTML . 'data/upload/');
	
	define ('__DATA_PATH', __SITE_PATH . '/public_html/data/');
	define ('__DATA_URL', __PUBLIC_HTML . 'data/');

    $const = get_defined_constants(true);
    echo "<pre>";
    print_r($const['user']);
    echo "</pre>";
    exit();


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

    function run()
    {


        parent::run(); // TODO: Change the autogenerated stub
    }
}


$app = new Application();
$app->run();

