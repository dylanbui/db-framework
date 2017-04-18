<?php

namespace Admin\Controller;

use Admin\Lib\Support\UserAuth;
use TinyFw\Core\Controller;
use TinyFw\Support\View;

class AdminBaseController extends Controller
{
	protected $_isModify;

	public function __construct()
	{
		parent::__construct();
//        // --- Set oView Params ---//
//        $this->oView->oConfig = $this->oConfig;
	}
	
	protected function detectModifyPermission($route)
	{
//		if ($this->oAuth->hasPermission('modify',$route))
        $this->_isModify = FALSE;
        if (UserAuth::hasPermission('modify',$route))
			$this->_isModify = TRUE;

        View::setVars(array('allowModify' => $this->_isModify));
		return $this->_isModify;
	}

}
