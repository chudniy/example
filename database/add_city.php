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
$eu_countries = array(
	'at', 'be', 'hr', 'bg', 'cy', 'cz', 'dk', 'ee', 'fi', 'fr', 'de', 'gr', 'hu', 'ie',
	'it', 'lv', 'lt', 'lu', 'mt', 'nl', 'pl', 'pt', 'ro', 'sk', 'si', 'es', 'se', 'gb'
);

echo "Start\r\n";

$city_data_array = explode( "\n", file_get_contents( __DIR__ . '/data/cities.txt' ) );


echo "In process...\r\n";

foreach ( $city_data_array as $data_string ) {
  $data_array = explode(',', $data_string);

    if ( in_array($data_array[0], $eu_countries) && $data_array[4]) {
      Core::getMysql()->query( "INSERT INTO wp_city SET (?)", array(
      	'country_code' => $data_array[0],
      	'name' => $data_array[1],
      	'region' => $data_array[3],
      	'population' => $data_array[4],
      	'latitude' => $data_array[5],
      	'longitude' => $data_array[6],
      ) );

      echo $data_array[0] .' - ' . $data_array[1] .' - ' . $data_array[3] .' - ' . $data_array[4]. "\r\n";
    }
  }


echo 'Finished!';
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
 