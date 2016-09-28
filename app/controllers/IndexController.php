<?php

namespace App\Controller;

use TinyFw\Core\Controller;
use TinyFw\Helper\Captcha;
use App\Lib\Email;

class IndexController extends Controller
{

	public function __construct()
	{
		parent::__construct();
//		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	}

	public function indexAction() 
	{
        echo "<pre>";
        print_r('Trang index/index');
        echo "</pre>";
        exit();
////		$_SESSION['test'] = 12;
////		$this->oSession->userdata['test'] = 12;
//        $this->oSession->set('test', 12);
////	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
//	    $this->renderView('site/home/index');
	}
	


	
	
}
