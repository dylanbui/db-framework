<?php  if ( ! defined('__SITE_PATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
|
|
*/

// http://www.codeigniter.com/user_guide/general/routing.html
// Khong nen tao router thanh 1 class vi co the khong toi uu
// Tham khoa class router co ban : http://codereview.stackexchange.com/questions/101364/simple-router-class

// $routes['(:any)'] = "router/$1";
// $routes['products/(:any)'] = "category/$1";
// $routes['products/([a-z]+)/(\d+).html'] = "$1/abc_$2";
// $routes['links/([a-zA-Z0-9_-]+)'] = "site/index/links/$1";

//$routes['email/(:name)/(:name)/(:any)'] = array('path' => '$1/$2/$3', 'namespace' => 'App\Controller\Email');
//
//$routes['site-index/(:name)/(:name)'] = array('path' => '$1/$2', 'namespace' => 'App\Controller\SiteIndex');
//$routes['site-index/(:name)/(:name)/(:any)'] = array('path' => '$1/$2/$3', 'namespace' => 'App\Controller\SiteIndex');

//$routes['links/chuyen-tieng-viet/(:num)'] = "site-index/index/links-item";
//$routes['links/chuyen-tieng-viet'] = "site-index/index/links-item";

$routes['links/(:any)'] = "site-index/links?link=$1";
$routes['links/(:any).html'] = "site-index/links?html=$1";
$routes['my-site/(:name)/(:any).html'] = "site-index/links/$2?html=$1";
//
//$routes['load/(:any)-post(:num).htm'] = "site-index/index/load-router/$1/$2";
//$routes['load/(:any)-post(:num).html'] = "site-index/index/load-router/$1/$2";
//
// -- Config router for namespace --
//$routes['paging'] = array('path' => 'index/index', 'namespace' => 'App\Controller\Paging');
//$routes['paging/(:name)'] = array('path' => '$1/index', 'namespace' => 'App\Controller\Paging');
//$routes['paging/(:name)/(:name)'] = array('path' => '$1/$2', 'namespace' => 'App\Controller\Paging');
//$routes['paging/(:name)/(:name)/(:any)'] = array('path' => '$1/$2/$3', 'namespace' => 'App\Controller\Paging');

$routes['paging/(:other)'] = array('path' => '$1', 'namespace' => 'App\Controller\Paging');

// -- Config router for namespace (FULL) --
//$routes['database'] = array('path' => 'index/index', 'namespace' => 'App\Controller\Database');
//$routes['database/(:name)'] = array('path' => '$1/index', 'namespace' => 'App\Controller\Database');
//$routes['database/(:name)/(:name)'] = array('path' => '$1/$2', 'namespace' => 'App\Controller\Database');
//$routes['database/(:name)/(:name)/(:any)'] = array('path' => '$1/$2/$3', 'namespace' => 'App\Controller\Database');

// -- Config router for namespace (SIMPLE) vd: database/model --
$routes['database/(:other)'] = array('path' => '$1', 'namespace' => 'App\Controller\Database');

// -- Namespace : App\Controller\SiteIndex --
$routes['site-index/(:other)'] = array('path' => '$1', 'namespace' => 'App\Controller\SiteIndex');

// $routes['links/(.*?)'] = "site/index/links/$1";
 
// $routes['journals'] 						= "blogs";
// $routes['blog/joe'] 						= "blogs/users/34";
// $routes['product/(:any)/(:any)'] 			= "catalog/product_lookup/$1/$2";
// $routes['product/(:num)'] 				= "catalog/product_lookup_by_id/$1";

$routes['blog/(:num)'] 						= function ($id)
{
    return 'catalog/product_view/' . $id;
};

$routes['blog/(:name)/(:num)'] 				= function ($product_type, $id)
{
    $result['namespace'] = 'App\Controller\SiteIndex';
    $result['path'] = 'catalog/product_edit/'.strtolower($product_type).'/'.$id.'/'.$_GET['title'];
    return $result;
};

// -- Demo REST FULL Api --
$routes['product/(:any)']['GET']             = "catalog/product_lookup_by_id_get/$1";
$routes['product/(:any)']['POST']             = "catalog/product_lookup_by_id_post/$1";
$routes['product/(:any)']['PUT']             = "catalog/product_lookup_by_id_put/$1";
$routes['product/(:any)']['DELETE']             = "catalog/product_lookup_by_id_delete/$1";


return $routes;