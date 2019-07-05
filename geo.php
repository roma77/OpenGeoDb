<?php
require( 'wp-load.php' );
ini_set('default_charset', 'utf-8');

global $newdb;	

// Chrnge you setings for connect to OpenGeoDb database: username, password, db_name, host
$newdb = new wpdb('root', '', 'opengeodb', 'localhost');

$enter_zip_code = '';
if( isset($_POST['zip_code']) )
{
	$zip_code = $_POST['zip_code'];
	$enter_zip_code = $newdb->get_results( "SELECT a.city_id, b.name, b.lat, b.lng FROM zipcode as a 
	JOIN city AS b ON a.city_id = b.id
	WHERE a.zipcode = $zip_code
	LIMIT 1
	" ); 
}

if ( $enter_zip_code ) {	
	$lat = $enter_zip_code[0]->lat;
	$lng = $enter_zip_code[0]->lng;
	$city_id = $enter_zip_code[0]->city_id;
	
	$zip_code = $_POST['zip_code'];
	$results_city = $newdb->get_results( "SELECT name FROM city 
	WHERE id != $city_id 
	AND (ACOS(SIN(PI() * $lat / 180.0) * SIN(PI() * lat / 180.0) 
	+ COS(PI() * $lat/180.0) * COS(PI() * lat / 180.0) 
	* COS(PI() * lng / 180.0 - PI() * $lng / 180.0)) * 6371 )
	< 10
 
	" );	
}

?>
	<form method="post">
		<label for="zip_code">Enter zip code</label>
		<input id="zip_code" name="zip_code" type="number" min="0" step="1"></input>
		<input type="submit" value="Get city in radius 10km">
	</form>
<?php

if ( isset($_POST['zip_code']) && $enter_zip_code ) {
	?>
	<h2>You Enter <?=$_POST['zip_code'];?> <?=$enter_zip_code[0]->name;?></h2>
	<?php
	if ($results_city){
		echo '<h3>Cityes in radius 10 km:</h3>';
		foreach ($results_city as $cityes){			
			echo '<p>' . $cityes->name . '</p>';
		}
	} else {
		echo '<h3>Cityes in radius 10 km not found!</h3>';
	}
} elseif ( isset($_POST['zip_code']) && !$enter_zip_code ){
	?>
	<h2>City Not Found</h2>
	<?php
}

