<?php

namespace App\Model\Atm;

use TinyFw\Core\Model;

class Bank extends Model
{
	protected $_table_name = TB_ATM_BANK;
	protected $_primary_key = 'bank_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	public function getRelationBankWithBank($bankId)
    {
        $rowBank = $this->getRow('bank_id = ?', array($bankId));
        return $this->getRowset("bank_id IN (".$rowBank['relation_bank_id'].")");
    }
	
}

?>