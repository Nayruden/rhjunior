<?php
error_reporting(E_ALL);
$grab_url = "http://www.rhjunior.com/QQSR/Images/";
$num = 1;

// create a cURL handle
$ch = curl_init();

while ( true ) { // Break inside
	// set URL and other options
	curl_setopt( $ch, CURLOPT_BINARYTRANSFER, TRUE ); // We're grabbing images
	curl_setopt( $ch, CURLOPT_FILETIME, TRUE ); // We want the modify date

	curl_setopt( $ch, CURLOPT_URL, $grab_url . sprintf( "%05d.png", $num ) );
	curl_setopt( $ch, CURLOPT_HEADER, FALSE );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );

	// grab URL and pass it to the browser
	$ret = curl_exec( $ch );

	$info = curl_getinfo( $ch );
	printf( "Img: %s %s", sprintf( "imgs/%05d.png", $num ), $info[ 'http_code' ] );
	if ( $info[ 'http_code' ] != '200' )
		break;
		
	$fh = fopen( sprintf( "imgs/%05d.png", $num ), "w" );
	fwrite( $fh, $ret );
	fclose( $fh );
	touch( sprintf( "imgs/%05d.png", $num ), intval( $info[ 'filetime' ] ) );
	
	$num++;
}	

// close cURL resource, and free up system resources
curl_close( $ch );
?>
