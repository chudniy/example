<?php
header( 'Content-Type: text/html; charset=utf-8' );

include_once( __DIR__ . '/../wp-config.php' );
include_once( __DIR__ . '/code/Tech/Object.php' );
include_once( __DIR__ . '/code/Core.php' );
include_once( __DIR__ . '/code/Tech/Model/Tech.php' );
include_once( __DIR__ . '/code/Tech/Model/Filesystem.php' );
include_once( __DIR__ . '/code/Tech/Model/Html.php' );
include_once( __DIR__ . '/code/Core/Model/Mysql.php' );

//--- установка кодировки
Core::getMysql()->query( "SET NAMES 'utf8' COLLATE  'utf8_general_ci'" );

//--- alpha2 codes
$eu_codes =  array(
	'at', 'be', 'hr', 'bg', 'cy', 'cz', 'dk', 'ee', 'fi', 'fr', 'de', 'gr', 'hu', 'ie',
	'it', 'lv', 'lt', 'lu', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'si', 'es', 'se', 'gb'
);

echo "Start\r\n";

foreach ( $eu_codes as $code ) {

	$url = 'https://restcountries.eu/rest/v2/alpha/' . $code;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_exec($ch);
	$data = curl_multi_getcontent($ch);
	curl_close($ch);

	$data = json_decode( $data );

	Core::getMysql()->query( "INSERT IGNORE INTO wp_country SET (?)", array(
		'code' => $code,
		'name' => $data->name,
		'capital' => $data->capital,
		'population' => $data->population,
		'latitude' => $data->latlng[0],
		'longitude' => $data->latlng[1],
	) );
  }


echo 'Finished!';

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
 