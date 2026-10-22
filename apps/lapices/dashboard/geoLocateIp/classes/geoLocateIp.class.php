<?php
/**
  * geoLocateIp PHP class
  * v.00001
  * By Campbell.sx
  * 
  *
  *
  * -----------------------------------------------------------
  *
  * Copyright 2008 Omar Pera
  * 
  * -----------------------------------------------------------
  *
  * LICENSE
  *
  * This program is free software; you can redistribute it and/or 
  * modify it under the terms of the GNU General Public License as 
  * published by the Free Software Foundation; either version 2 of 
  * the License, or (at your option) any later version. 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  * GNU General Public License for more details. You should have 
  * received a copy of the GNU General Public License along with 
  * this program; if not, write to the Free Software Foundation, 
  * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
  *
  * To read the license please visit http://www.gnu.org/licenses/gpl.txt
  *
  */

class geoLocateIp
{
	private $serviceLocateURL = 'http://api.hostip.info/?ip=';

    public function getLocationFromIp()
    {
		$ip = $this->getIpAdress();
		
		if (empty($ip))
			throw new Exception('Error retrieving IP address');
			
		// Use the method your server supports ( most of them only support curl )
		$xmlData = geoLocateIp::file_get_contents_curl($this->serviceLocateURL.$ip);
		//$xmlData = file_get_contents($this->serviceLocateURL.$ip);
		
		if (empty($xmlData))
			throw new Exception('Error retrieving xml');

		$locationInfo = $this->parseLocationData($xmlData);
		
		return $locationInfo;
    }


    private function getIpAdress()
    {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			elseif (isset($_SERVER['HTTP_VIA'])) {
			   $ip = $_SERVER['HTTP_VIA'];
			}
			elseif (isset($_SERVER['REMOTE_ADDR'])) {
			   $ip = $_SERVER['REMOTE_ADDR'];
			}
			else {
			   $ip = NULL;
			}
		return $ip;
    }

	private function parseLocationData($xmlData)
	{
		// Use of Simple XML extension of PHP 5
		$xml = simplexml_load_string($xmlData);

		if (!is_object($xml))
		    throw new Exception('Error reading XML');
		
		$infoHost = $xml->xpath('//gml:featureMember');
		$city = $xml->xpath('//gml:featureMember//gml:name');

		$coordinates = $infoHost[0]->xpath('//gml:coordinates');
		$coordinates = split(',', (string) $coordinates[0]);				
		
		$info = array (
			"City"  			=> (string) $city[0],
			"CountryName" 		=> (string)	$infoHost[0]->Hostip->countryName,
			"CountryCode" => (string)	$infoHost[0]->Hostip->countryAbbrev,
			"Longitude"			=> $coordinates[0],
			"Latitude"			=> $coordinates[1]
		);
		
		return $info;
	}

	public static function file_get_contents_curl($url) 
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
	
		$data = curl_exec($ch);
		curl_close($ch);
	
		return $data;
	}
}
?>
