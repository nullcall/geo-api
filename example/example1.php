<?php 

//composer require nullcall/geo-api
use gmap\MapAPI;
include "vendor/autoload.php";



/// variables 
	$lat = '22.453731';
	$long ='74.623032';
	$coDis = array(
				'0' => array('lat' => '18.92237167', 'long' => '72.827865' ),
				'1' => array('lat' => '18.94096833', 'long' => '72.83850833' ),
				// '2' => array('lat' => '28.463731', 'long' => '74.64032' ),
				// '3' => array('lat' => '28.50731', 'long' => '75.66032' ),
				);

	echo "<PRE>";
/// Fetch Address for given coordinates ///
	$gc = MapAPI::SetLatLong($lat,$long);
	print_r($gc->addFetch());

/// Call Object to init the value in main Class ////////
	$gc = MapAPI::setDisctance($coDis,$unit = 'k');
	print_r($gc->calDistance());
	echo "<br />";
/// Call Object to for direction API
	$direction = MapAPI::setDirection($coDis);
	print_r($direction->getDirection());
/// Call Object to for direction API
	$direction = MapAPI::setDirection($coDis);
	print_r($direction->getDirectionAngle());