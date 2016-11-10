<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
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

		$this->renderView('dashboard/show');
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
        $menuInfo = Config::get('left_menus');
        return View::fetch('dashboard/nav', array('menuInfo' => $menuInfo));
	}

}
