<?php
require_once( './defines.php' );
error_reporting( E_ALL );
$config_file = "comicstats.txt";

if ( !file_exists( $config_file ) ) {
	$fh = fopen( $config_file, 'w' ) or die ('Unable to create config file!');
	fclose( $fh );
}

$contents = file_get_contents( $config_file );
$lines = split( "\n", $contents );
$data = array();

foreach ( $lines as $line ) {
	$line = trim( $line );
	if ( !empty( $line ) ) {
		list( $key, $value ) = split( ' ', $line );
		$data[ $key ] = $value;
	}
}
print_r( $data );

$ch = curl_init();
curl_setopt( $ch, CURLOPT_NOBODY, TRUE ); // We just want the server response
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE ); // Don't output retrn

foreach ( $knownRHJComics as $key => $comic ) {
	$num = 0;
	$curSkip = 0;
	if ( isset( $data[ $key ] ) )
		$num = intval( $data[ $key ] ) - 1;	
    
	do {
		$num++;
		$filename = sprintf( "./%05d.png", $num );
		curl_setopt( $ch, CURLOPT_URL, $comic . $filename ); // The URL of course

		$ret = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		
		// Try .gif
		if ( $info[ 'http_code' ] == 404 ) {
			$filename = sprintf( "./%05d.gif", $num );
			curl_setopt( $ch, CURLOPT_URL, $comic . $filename ); // The URL of course
			$ret = curl_exec( $ch );
			$info = curl_getinfo( $ch );
			
			if ( $info[ 'http_code' ] == 404 ) {
				$curSkip++;
			}
		} else {
			$curSkip = 0;
		}
	} while ( $info[ 'http_code' ] == 200 || ($info[ 'http_code' ] == 404 && $curSkip <= $max_skip) );
	
	$data[ $key ] = $num - $curSkip;
}

$fh = fopen( $config_file, 'w' ) or die ('Unable to open config file!');
foreach ( $data as $key => $num ) {
	fwrite( $fh, "${key} ${num}\n" );
}
fclose( $fh );

print_r( $data );
?>