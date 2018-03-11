<?php
  /**
   * Created by PhpStorm.
   * User: Jack
   */
  include_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');
  include_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/database/code/Tech/Object.php');
  include_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/database/code/Core.php');
  include_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/database/code/Core/Model/Mysql.php');
  
  //--- установка кодировки
  Core::getMysql()->query("SET NAMES 'utf8' COLLATE  'utf8_general_ci'");
  
  $term = $_GET['term'];
  $country = $_GET['country'];
  $city = $_GET['city'];
  $data = array();
  
  if ($country)
  {
    $data = Core::getMysql()->getValuesAsOneArray("SELECT name FROM wp_country WHERE name LIKE '" . $term . "%'  GROUP BY name ORDER BY name LIMIT 5");
  }
  elseif ($city)
  {
    $data = Core::getMysql()->getValuesAsOneArray("SELECT name FROM wp_city WHERE name LIKE '" . $term . "%'   GROUP BY name ORDER BY name LIMIT 5");
  }

foreach ( $data as $key => $item ) {
  	$data[$key] = ucfirst($item);
}
  
  echo json_encode($data);