<?php

namespace App\Model;

class District extends \App\Model\Base\Model
{
	protected $_table_name = TB_DISTRICTS;
	protected $_primary_key = 'district_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}
	
?>