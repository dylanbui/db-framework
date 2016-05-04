<?php

namespace App\Model\Atm;

use App\Lib\Core\Model;

class Bank extends Model
{
	protected $_table_name = TB_BANKS;
	protected $_primary_key = 'bank_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	
}

?>