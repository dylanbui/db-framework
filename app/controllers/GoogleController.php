<?php

namespace App\Controller;

use App\Lib\Paginator;
use App\Model\Atm\Address as AtmAddress;
use App\Model\Atm\Bank as AtmBank;
use Geocoder\Exception\ExtensionNotLoaded;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;
use TinyFw\Core\Controller;


class GoogleController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function indexAction() 
	{
	    $this->listAtmAddressAction();
	}

    public function listAtmAddressAction($bankId = 59)
    {
        $viewData = array();
        $viewData['title'] = 'Atm Address';

        $atmAddress = new AtmAddress();
        $objAtmBank = new AtmBank();

        $rsRelationBank = array();
        if ($bankId != 0) {
            $rs = $atmAddress->getRowSet('bank_id = ?' ,array($bankId));

            $rsRelationBank = $objAtmBank->getRelationBankWithBank($bankId);
            foreach ($rsRelationBank as &$rowRelationBank)
            {
                $rsAddress = $atmAddress->getRowSet('bank_id = ?' ,array($rowRelationBank['bank_id']));
                $rowRelationBank['listAddress'] = $rsAddress;
            }
        }
        else
            $rs = $atmAddress->getRowSet();

        $viewData['rs'] = $rs;
        $viewData['rsRelationBank'] = $rsRelationBank;

        $rsBank = $objAtmBank->getRowSet();

        $viewData['bankId'] = $bankId;
        $viewData['rsBank'] = $rsBank;

        $this->renderView('google/get_atm_address', $viewData);
    }


    public function getLatLongFromGoogleAction($offset = 0)
    {
        $objAtmAddress = new AtmAddress();

//        $rsAddress = $objAtmAddress->getRowset(NULL, NULL, NULL, $offset, 20);
        $rsAddress = $objAtmAddress->getRowset('address_id >= 97 AND address_id <= 109', NULL, NULL);

        $curlAdapter = new CurlHttpAdapter();
        $geocoder = new GoogleMaps($curlAdapter);

        foreach ($rsAddress as $row)
        {
            try {
                $address = $geocoder->geocode($row['address'])->first();
                if (empty($address))
                {
                    echo 'Loi truy van id = '.$row['address_id'].', address = '.$row['address'].'<br>';
                    continue;
                }
            } catch (\Exception $ex)
            {
                echo 'Loi [LOAD] id = '.$row['address_id'].', address = '.$row['address'].'<br>';
                continue;
            }

            echo "<pre>";
            print_r($row['address']);
            echo "</pre>";

            $arr['lat'] = $address->getLatitude();
            $arr['long'] = $address->getLongitude();

            $objAtmAddress->update($row['address_id'], $arr);
        }

        echo "<br><pre>";
        print_r('DONE');
        echo "</pre>";
        exit();

        $this->renderView('site-index/index/index');
    }

    public function getLatLongFromAddressAction()
    {
        $curlAdapter = new CurlHttpAdapter();
        $geocoder = new GoogleMaps($curlAdapter);

        $address = $geocoder->geocode('96 Lý Tự Trọng, P. Bến Thành, Q.1, TP. Hồ Chí Minh')->first();
//        $geocoder->reverse(...);

        echo "<pre>";
        print_r($address->getCoordinates());
        echo "</pre>";

        echo "<pre>";
        print_r($address->getLatitude());
        echo "</pre>";

        echo "<pre>";
        print_r($address->getLongitude());
        echo "</pre>";



        echo "<pre>";
        print_r($address);
        echo "</pre>";
        exit();
    }

    public function getLatLongAction()
    {
        $result = $this->getLatLong('96 Lý Tự Trọng, P. Bến Thành, Q.1, TP. Hồ Chí Minh');

        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit();
    }


    /**
     * Author: CodexWorld
     * Author URI: http://www.codexworld.com
     * Function Name: getLatLong()
     * $address => Full address.
     * @param string $address => Full address.
     * @return Latitude and longitude of the given address.
     **/
    // http://www.codexworld.com/get-latitude-longitude-from-address-using-google-maps-api-php/
    private function getLatLong($address)
    {
        if(!empty($address)){
            //Formatted address
            $formattedAddr = str_replace(' ','+',$address);
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false');
            $output = json_decode($geocodeFromAddr);
            //Get latitude and longitute from json data
            $data['latitude']  = $output->results[0]->geometry->location->lat;
            $data['longitude'] = $output->results[0]->geometry->location->lng;
            //Return latitude and longitude of the given address
            if(!empty($data)){
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
