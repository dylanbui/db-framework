<?php

namespace App\Controller;

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
	    $this->renderView('site-index/index/index');
	}

    public function getLatLongFromAddressAction()
    {
        $curlAdapter = new CurlHttpAdapter();
        $geocoder = new GoogleMaps($curlAdapter);

        $result = $geocoder->geocode('96 Lý Tự Trọng, P. Bến Thành, Q.1, TP. Hồ Chí Minh');
//        $geocoder->reverse(...);


        echo "<pre>";
        print_r($result);
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
