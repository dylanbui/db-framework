<?php

namespace Admin\Controller;

use App\Model\Admin\Group;
use App\Model\Admin\ModuleAcls;
use App\Model\Page\Configure;
use TinyFw\Helper\Text;
use TinyFw\Support\Input;

class GroupController extends AdminBaseController
{

	public function __construct()
	{
		parent::__construct();
//		$this->detectModifyPermission('home/group');
//		$this->oView->_isModify = $this->_isModify;

	}

	public function indexAction() 
	{
		$ignore = array(
				'common/common',
				'common/error',
				'common/home',
				'home/dashboard'
		);
		
		$permissions = array();
		
		$files = glob(__APP_PATH . '/controllers/*/*.php');
		
		foreach ($files as $file) 
		{
			$data = explode('/', dirname($file));
		
			$permission = end($data) . '/' . camelcaseToHyphen(basename($file, 'Controller.php'));
		
			if (!in_array($permission, $ignore)) {
				$permissions[] = $permission;
			}
		}

		// -- Show debug permissions --
		echo "<pre>";
		print_r($permissions);
		echo "</pre>";
		exit();
	}

	
	public function listAction() 
	{
		$objGroup = new Group();
		$rsGroups = $objGroup->getRowset();

        $viewData = array(
            'box_title' => 'Edit Group',
            'rsGroups' => $rsGroups
        );

		$this->renderView('group/list', $viewData);
	}
	
	public function addAction()
	{
//		if (!$this->_isModify)
//			return $this->forward('error/error-deny');
				
		if ($this->oInput->isPost())
		{
			$group_name = Input::post('group_name','');
			$role = Text::strToUrl(trim($group_name),"_");
			
			$permission = Input::post('permission',NULL);
			$is_admin = Input::post('is_admin','0');
			
			// TODO : Check validate
			if ($permission == NULL) { }
			
			$permission = serialize($permission);
			
			$data = array(
				"role" => $role,					
				"group_name" => $group_name,
				"level" => Input::post('level','0'),
				"is_admin" => $is_admin,
				"acl_resources" => $permission					
			);
			
			$objGroup = new Group();
			$last_id = $objGroup->insert($data);
			
			redirect("group/list");
		}

        $viewData['box_title'] = "Add Group";
		
		$acls = new ModuleAcls(__APP_PATH.'/config/acls.php');
        $viewData['arrAcls'] = $acls->getModuleAcls();
        $viewData['link_url'] = site_url('group/add');
        $viewData['cancel_url'] = site_url('group/list');

		$objPageConf = new Configure();
        $viewData['rsPageConfig'] = $objPageConf->getRowset();
        $viewData['arrAclResources'] = array('access'=>array(), 'modify'=>array());

		$this->renderView('group/_form', $viewData);
	}
	
	public function editAction($group_id)
	{
//		if (!$this->_isModify)
//			return $this->forward('error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		
		if ($this->oInput->isPost())
		{
			$group_name = Input::post('group_name','');
            // -- Khong cho thay doi role --
			$permission = Input::post('permission',NULL);
			$is_admin = Input::post('is_admin','0');
            $active = Input::post('active','0');
				
			// TODO : Check validate
			if ($permission == NULL){}
				
			$permission = serialize($permission);
				
			$data = array(
					"group_name" => $group_name,
					"level" => Input::post('level','0'),
					"is_admin" => $is_admin,
                    "active" => $active,
					"acl_resources" => $permission
			);

			$objGroup->update($group_id,$data);
			redirect("group/list");
		}		
		
        $viewData['box_title'] = "Edit Group";
		
		$acls = new ModuleAcls(__APP_PATH.'/config/acls.php');
        $viewData['arrAcls'] = $acls->getModuleAcls();
        $viewData['link_url'] = site_url('group/edit/'.$group_id);
        $viewData['group_id'] = $group_id;
        $viewData['cancel_url'] = site_url('group/list');

		$objPageConf = new Configure();
        $viewData['rsPageConfig'] = $objPageConf->getRowset();

		$rowGroup = $objGroup->get($group_id);
        $viewData['rowGroup'] = $rowGroup;
        $viewData['arrAclResources'] = unserialize($rowGroup['acl_resources']);

		$this->renderView('group/_form', $viewData);
	}
	
	public function activeAction($group_id)
	{
//		if (!$this->_isModify)
//			return $this->forward('error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		$objGroup->setActiveField($group_id);
		redirect("group/list");
	}
	
	public function deleteAction($group_id)
	{
//		if (!$this->_isModify)
//			return $this->forward('error/error-deny');
				
		// TODO : Check validate
		$objGroup = new Group();
		$rowGroup = $objGroup->delete($group_id);
	
		redirect("group/list");
	}	

}
