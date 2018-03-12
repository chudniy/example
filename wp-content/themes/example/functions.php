<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 29.06.2017
 * Time: 16:20
 */
include_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/database/code/Tech/Object.php' );
include_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/database/code/Core.php' );
include_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/database/code/Core/Model/Mysql.php' );
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-admin/includes/file.php';

define( 'RESULT_PAGE_ID', 12 );

add_theme_support( 'menus' );
add_action( 'wp_enqueue_scripts', 'wp_adding_styles' );
add_action( 'wp_enqueue_scripts', 'wp_adding_scripts' );
register_nav_menus( array(
	'delivery_left'   => 'слева',
	'delivery_center' => 'по центру',
	'delivery_right'  => 'справа',
) );

/**
 * SCRIPTS
 */
function wp_adding_scripts() {
	wp_enqueue_script( 'jquery_ui',
		get_template_directory_uri() . '/assets/lib/jquery-ui-1.12.1.custom/jquery-ui.min.js', array( 'jquery' ), false,
		true );
	wp_enqueue_script( 'jquery_ui', get_template_directory_uri() . '/assets/lib/jquery-3.2.1.min.js', false, true );
	wp_enqueue_script( 'bootstrap',
		get_template_directory_uri() . '/bower_components/bootstrap/js/bootstrap.min.js', array( 'jquery' ), false,
		true );
	wp_enqueue_script( 'datatable', 'https://cdn.datatables.net/v/bs/dt-1.10.16/r-2.2.1/sc-1.4.4/datatables.min.js', array( 'jquery', 'bootstrap' ),false, true  );
	wp_enqueue_script( 'main', get_template_directory_uri() . '/main.js', array( 'jquery', 'jquery_ui', 'bootstrap' ),
		false, true );
	wp_enqueue_script( 'script', get_template_directory_uri() . '/assets/lib/script.js', array( 'jquery' ), false,
		true );

	//---  Вставка HTML5 поєднується з Respond.js для підтримки в IE8 елементів HTML5 та медіа-запитів
	wp_enqueue_script( 'html5shiv', 'https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js' );
	wp_enqueue_script( 'respond', 'https://oss.maxcdn.com/respond/1.4.2/respond.min.js' );

}


/**
 * STYLES
 */
function wp_adding_styles() {
	wp_enqueue_style( 'table', 'https://cdn.datatables.net/v/bs/dt-1.10.16/r-2.2.1/sc-1.4.4/datatables.min.css' );
	wp_enqueue_style( 'Lato', 'https://fonts.googleapis.com/css?family=Lato' );
	wp_enqueue_style( 'font-awesome',
		get_template_directory_uri() . '/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'bootstrap',
		get_template_directory_uri() . '/bower_components/bootstrap/css/bootstrap.css' );
	wp_enqueue_style( 'bootstrap-theme',
		get_template_directory_uri() . '/bower_components/bootstrap/css/bootstrap-theme.css' );
	wp_enqueue_style( 'jquery_ui_theme',
		get_template_directory_uri() . '/assets/lib/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css' );
	wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css' );
}


add_action( 'init', 'do_rewrite' );
function do_rewrite() {
	// Правило перезаписи
//	add_rewrite_rule( '^nutrition/([^/]*)/([^/]*)/?', 'index.php?p=12&food=$matches[1]&variety=$matches[2]', 'top' );
	add_rewrite_rule( '^result/([^/]*)/([^/]*)/?',
		'/index.php?p=12&?country=$matches[1]&country_population=$matches[2]', 'top' );

	// скажем WP, что есть новые параметры запроса
	add_filter( 'query_vars', function ( $vars ) {
		$vars[] = 'country';
		$vars[] = 'country_population';

		return $vars;
	} );
}


function getResults( $country = false, $country_pop = false, $city = false, $city_pop = false ) {
	$country = Core::getMysql()->escapeString( $country );
	$city    = Core::getMysql()->escapeString( $city );


	if ( $country_pop ) {
		switch ( $country_pop ) {
			case '500K':
				$country_pop_from = 1;
				$country_pop_to   = 499999;
				break;
			case '500K-5M':
				$country_pop_from = 500000;
				$country_pop_to   = 4999999;
				break;
			case '5M-45M':
				$country_pop_from = 5000000;
				$country_pop_to   = 44999999;
				break;
			case 'over-45M':
				$country_pop_from = 45000000;
				$country_pop_to   = 999999999;
				break;
			default:
				$country_pop_from = false;
				$country_pop_to   = false;
				break;
		}
	}

	if ( $city_pop ) {
		switch ( $city_pop ) {
			case '50K':
				$city_pop_from = 1;
				$city_pop_to   = 49999;
				break;
			case '50K-500K':
				$city_pop_from = 50000;
				$city_pop_to   = 499999;
				break;
			case '500K-1M':
				$city_pop_from = 500000;
				$city_pop_to   = 999999;
				break;
			case 'over-1M':
				$city_pop_from = 1000000;
				$city_pop_to   = 999999999;
				break;
			default:
				$city_pop_from = false;
				$city_pop_to   = false;
				break;
		}
	}


	$country_data = Core::getMysql()->getValues( "SELECT * 
											            FROM wp_country 
											            WHERE true
											            " . ( $country ? "AND name = '{$country}'" : null ) . " 
											            " . ( ( $country_pop_from && $country_pop_to ) ? "AND population BETWEEN {$country_pop_from} AND {$country_pop_to}" : null ) . " 
											            " );

	if ( $country_data ) {
		foreach ( $country_data as $country_row ) {
			$code_array[] = "'" . $country_row['code'] . "'";
		}

		$code_string = implode( ',', $code_array );
	} else {
		return false;
	}


	$city_data = Core::getMysql()->getValues( "SELECT city.name AS city_name, country.name AS country_name, country.population AS country_pop, city.population AS city_pop, country.capital AS capital 
										             FROM wp_city AS city
										             LEFT JOIN wp_country AS country ON city.country_code = country.code
										             WHERE true
										             " . ( $code_array ? "AND country_code IN ({$code_string})" : null ) . " 
										             " . ( $city ? "AND city.name = '{$city}'" : null ) . " 
										             " . ( ( $city_pop_from && $city_pop_to ) ? "AND city.population BETWEEN {$city_pop_from} AND {$city_pop_to}" : null ) . " 
										             " );

	return $city_data;
}
  
 
  

  