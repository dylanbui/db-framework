<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use App\Model\Page\Configure;
use TinyFw\Core\Controller;
use TinyFw\Support\Config;
use TinyFw\Support\View;

class DashboardController extends AdminBaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{		
// 		if(!$this->isLogged())
// 			redirect('common/home/login');

	    return $this->forward('dashboard/panel/show');
	}	
	
	public function showAction() 
	{
//        echo "<pre>";
//        print_r(UserAuth::currentUser());
//        echo "</pre>";
//        exit();

        $menuInfo = $this->fullNav();
        $arrConfigSystemNav = $menuInfo['config-system'];
        unset($menuInfo['config-system']);

        $dataView = array(
            'arrConfigSystemNav' => $arrConfigSystemNav,
            'menuInfo' => $menuInfo
        );

		$this->renderView('dashboard/show', $dataView);
	}
	
	public function formViewAction()
	{
		
		$this->renderView('dashboard/panel/form');
	}

	public function tableViewAction()
	{
	
		$this->renderView('dashboard/panel/table');
	}

	public function blankPageAction()
	{
		$this->renderView('dashboard/panel/blank');
	}

	public function permissionFormAction()
	{
        $acls = new Base_ModuleAcls(Config::get('acls'));
		$this->renderView('dashboard/panel/permission');
	}	
	
	public function renderLeftNavAction()
	{
        $menuInfo = $this->fullNav();
        return View::fetch('dashboard/nav', array('menuInfo' => $menuInfo));
	}

    private function fullNav()
    {
        $arrPageMenus = array();
        $objPageConf = new Configure();
        $rsPages = $objPageConf->getRowset("data <> ''");

//        $objPageCat = new Configure;
//        echo "<pre>";
//        print_r($rsPages);
//        echo "</pre>";
//        exit();

        foreach ($rsPages as $rowPage)
        {
            $key = 'page/content/list/'.$rowPage['code'];
            if (UserAuth::hasPermission('access',$key))
            {
                $arrTemp = array();
                $arrTemp['name'] = $rowPage['name'];
                $arrTemp['icon'] = $rowPage['icon'];//"icon-list-alt";

                $data = unserialize($rowPage['data']);

                $arrSub = array();
                // 				$rsCat = $objPageCat->getRowset('page_id = ?', array($rowPage['id']));
                // 				if (count($rsCat))
                if (df($data['use_category'], 0) == 1)
                {
                    $arrSub[] = array("icon"=>"icon-folder-open", "name" => "Category","link" => "page/category/list/".$rowPage['code']);
                }
                $arrSub[] = array("icon"=>"icon-list", "name" => "List","link" => "page/content/list/".$rowPage['code']);
                $arrSub[] = array("icon"=>"icon-edit","name" => "Add new","link" => "page/content/add/".$rowPage['code']);
                $arrTemp['sub_menus'] = $arrSub;

                $arrPageMenus[$key] = $arrTemp;
            }
        }


        $menuInfo = array();
        $menus = Config::get('left_menus');

        foreach ($menus as $key => $menu)
        {
            $arrTemp = array();
            $arrTemp['name'] = $menu['name'];
            $arrTemp['icon'] = $menu['icon'];

            foreach ($menu['sub_menus'] as $sub_menu)
            {
                if (UserAuth::hasPermission('access',$sub_menu['key']))
                {
                    $arrTemp['sub_menus'][] = $sub_menu;
                }
            }

            if (!empty($arrTemp['sub_menus']))
            {
                $menuInfo[$key] = $arrTemp;
            }
        }
        return array_merge($arrPageMenus,$menuInfo);
    }


}
