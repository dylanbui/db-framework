<?php

namespace App\Controller;

use TinyFw\Core\Controller;
use TinyFw\Support\View;

class CommonController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function preControllerFirstAction()
	{
        $this->oView->preControllerFirst = 'preControllerFirstAction';
	}

    public function preControllerSecondAction($param_1, $param_2, $param_3)
    {
        View::setVars(array('preControllerSecond' => 'preControllerFirstAction - '.$param_1.' - '.$param_2.' - '.$param_3));
    }

    // -- Post controller after load View --
    public function postControllerFirstAction()
    {
//        file_put_contents(__DATA_PATH.'postControllerFirst-'.time().'.html', View::getContent());
    }

    // -- Post controller after load View --
    public function postControllerSecondAction($param_1, $param_2, $param_3)
    {
//        file_put_contents(__DATA_PATH.'postControllerSecond-'.time().'.html', View::getContent());
    }

	
}
