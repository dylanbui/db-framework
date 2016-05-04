<?php

namespace App\Model;

class City extends \App\Model\Base\Model
{
	protected $_table_name = TB_CITIES;
	protected $_primary_key = 'city_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}
	
?>