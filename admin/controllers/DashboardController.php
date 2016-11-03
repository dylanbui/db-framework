<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use TinyFw\Core\Controller;
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
		$acls = new Base_ModuleAcls(__APP_PATH.'/config/acls.php');
		$this->renderView('dashboard/panel/permission');
	}	
	
	public function renderLeftNavAction()
	{
		$menuInfo = require_once(__APP_PATH.'/config/left_menus.php');
        return View::fetch('dashboard/nav', array('menuInfo' => $menuInfo));
//		$this->oView->menuInfo = $menuInfo;
//		return $this->oView->fetch('dashboard/panel/nav');
	}	

}
