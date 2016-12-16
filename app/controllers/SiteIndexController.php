<?php

namespace App\Controller;

use TinyFw\Core\Controller;


class SiteIndexController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
	    $this->renderView('site-index/index/index');
	}

    public function homeAction()
    {
        echo "<pre>";
        print_r($_GET);
        echo "</pre>";
        exit();
    }

    public function linksAction()
    {
        echo "123123<pre>";
        print_r($_GET);
        echo "</pre>";
        exit();
    }

	
	
}
