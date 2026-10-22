<?php

/**
 * Geolocate your ip with hostip API
 * class developed by Omar Pera <campbell.sx@gmail.com>
 */

include('./classes/geoLocateIp.class.php');

$geo = new geoLocateIp();
$location = $geo -> getLocationFromIp();

/**
* Customizing your map with Phoogle Maps
* class developed by Justin Johnson <justinjohnson@system7designs.com>
*/

include('./classes/phoogle.php');

$map = new PhoogleMap();
$map->setAPIKey("ABQIAAAA46q6CwYAzRZ6b6ZsixmkuxRvy52B0AK04vFOv8wHPacaOuvFjRRPv1e-I6zJOH-IrDWa6SJaOKbD-g");


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
	<head>
		<meta http-equiv="Content-Type" content="text/html;" />

		<title>Geolocate your IP</title>

		<link rel="stylesheet" type="text/css" media="screen" href="./css/styles.css" />
		
		<?php $map->printGoogleJS(); ?>	
	  
	</head>
	<body>
		<div id="header">
			<h1><a href="./" title="Geolocate your IP">Geolocate your IP</a></h1>
		</div>
		
		<div id="content">
			  <?php
			    //customization options are here
			    $map->zoomLevel = 5;             //zoom in as far as we can
			    $map->setWidth(450);            //pixels
			    $map->setHeight(420);           //pixels
			    $map->controlType = 'large';    //show large controls on the side
			    $map->showType = false;         //hide the map | sat | hybrid buttons
				
				if ( empty($location['CountryName']) )
				{	
					echo "<h4>Good place to be... :)</h4>";
					$map->addAddress("350 5th Ave, New York, NY 10018, USA", "Cannot geolocate your ip");
					$map->showMap();
				}
				else
				{
					if (empty($location['Latitude']) || empty($location['Longitude']) )
					{
						$map->zoomLevel = 13;             
						$map->addAddress($location['CountryName'], "You are in " . $location['CountryName'] );
						$map->showMap();
					}
					else
					{	
						$map->addGeoPoint($location['Latitude'], $location['Longitude'],"You are in ". $location['City'].
										", located in ".$location['CountryName']." ( ".$location["CountryCode"]." )");
						$map->showMap();
					}
				}
			?>						
		</div>


			
		<div id="footer">
			<p>Powered by <a href="http://www.el33.es">Campbell</a> using Google Maps and <a href="http://www.www.hostip.info/">Hostip API</a></p>
		</div>
		
	</body>
</html>