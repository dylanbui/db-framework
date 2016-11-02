<?php

namespace Admin\Controller;

use TinyFw\Core\Controller;

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
		if ($this->oAuth->hasPermission('modify',$route))
			$this->_isModify = TRUE;
		
		return $this->_isModify;
	}

}
