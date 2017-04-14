<?php

namespace App\Controller;

use App\Lib\Paginator;
use App\Model\Atm\Address as AtmAddress;
use App\Model\Atm\Bank as AtmBank;
use Geocoder\Exception\ExtensionNotLoaded;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;
use TinyFw\Core\Controller;


class FacebookController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
        $this->renderView('facebook/login');
	}

    public function loginAction()
    {
        $this->renderView('facebook/login');
    }


}
