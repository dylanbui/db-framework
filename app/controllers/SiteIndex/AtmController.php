<?php

namespace App\Controller\SiteIndex;

use App\Lib\Paginator;
use App\Model\City;
use App\Model\District;
use App\Model\Atm\Address as Atm_Address;
use App\Model\Atm\Bank as Atm_Bank;
use TinyFw\Core\Controller;

class AtmController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
        $this->listAction();
	}

    public function listAction($bank_id = 0, $offset = 0)
    {
        $objAtmBank = new Atm_Bank();
        $arrAtmBank = $objAtmBank->getRowSet(NULL,array(),'bank_id DESC');
        $rowAtmBank = $objAtmBank->getRow('bank_id = ?',array($bank_id),'bank_id DESC');
        $this->oView->bank_id = $bank_id;
        $this->oView->rowAtmBank = $rowAtmBank;
        $this->oView->arrAtmBank = $arrAtmBank;

        $objAtmAddress = new Atm_Address();

        $items_per_page = 10;
        $offset = ($offset % $items_per_page != 0 ? 0 : $offset);

        $arrAtmAddress = $objAtmAddress->getRowSet((empty($bank_id) ? null : 'bank_id = ?'),
            array($bank_id),'address_id DESC',$offset,$items_per_page);
        $total = $objAtmAddress->getTotalRow();

        $pages = new Paginator();
        $pages->current_url = site_url("site-index/atm/list/{$bank_id}/%d");
        $pages->offset = $offset;
        $pages->items_per_page = $items_per_page;

        $pages->items_total = $total;
        $pages->mid_range = 7;
        $pages->paginate();

        $arrAtmAddressRelation = null;
        if (!empty($bank_id))
            $arrAtmAddressRelation = $objAtmAddress->getRowSet("bank_id IN ({$rowAtmBank['relation_bank_id']})");

        $this->oView->title = 'Danh Sach Atm';
        $this->oView->arrAtmAddress = $arrAtmAddress;
        $this->oView->pages = $pages;
        $this->oView->arrAtmAddressRelation = $arrAtmAddressRelation;

        $this->renderView('site-index/atm/list');
    }

	
}
