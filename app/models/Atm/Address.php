<?php

namespace App\Model\Atm;

use TinyFw\Core\Model;

class Address extends Model
{
	protected $_table_name = TB_ATM_ADDRESS;
	protected $_primary_key = 'address_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}

?>