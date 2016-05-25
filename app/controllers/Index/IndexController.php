<?php

namespace App\Controller\Index;
use App\Lib\Core\BaseController;

class IndexController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
//		$this->oView->title = 'Welcome to Bui Van Tien Duc MVC';
	}

	public function indexAction() 
	{
//		$_SESSION['test'] = 12;
		$this->oSession->userdata['test'] = 12;
	    $this->oView->title = 'Welcome to my site index/index/index MVC';
	    $this->renderView('site-index/home/index');
	}

    public function loadNewestListingAction($id_1, $id_2)
    {
        $this->oSession->userdata['test'] = 12;
        $this->oView->title = 'Welcome to index/index/loadNewestListingAction MVC';
        $this->renderView('site-index/home/index');

        $this->renderView('site-index/home/index');
    }

    public function loadJsonAction()
    {
        $data['val_1'] = 'Welcome to index/index/loadNewestListingAction MVC';
        $data['val_2'] = 'Welcome to index/index/loadNewestListingAction MVC';
        $data['val_3'] = 'Welcome to index/index/loadNewestListingAction MVC';
        $data['val_4'] = 'Welcome to index/index/loadNewestListingAction MVC';
        $data['val_5'] = array('name' => 'Tien Duc', 'value' => 'Gia tri');

        $this->oResponse->setOutputJson($data, 3);
    }

	
}
