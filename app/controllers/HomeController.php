<?php

namespace App\Controller;

use TinyFw\Core\Controller;
use TinyFw\Core\Request;
use TinyFw\Support\Request as RequestSupport;
use TinyFw\Logger;
use TinyFw\Support\Config;
use TinyFw\Support\Session;

class HomeController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function pageHomeAction($name = 'nguyen van a')
	{

        $this->renderView('site-index/home/index');

//		$this->oSession->set('test_1', 'Thong tin duoc luu vao test');
//		$this->oSession->set('test', 'Thong tin duoc luu vao test');
//
//        Session::set('support_session', 'Thanh cong roi ban oi');
//
//	    $this->oView->title = 'Welcome to Bui Van Tien Duc MVC RENDER';
//
//	    $this->renderView('site-index/home/index');
	}
	



}
