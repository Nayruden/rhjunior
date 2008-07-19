<?php
error_reporting( E_ALL ); // Since this is just a utility script.
require_once( './defines.php' );

function failure( $msg='' )
{
	echo "<h1>Failure</h1><br />\n";
	if ( !empty( $msg ) )
		echo $msg;
}

/*
	Function: grabComics
	
	Grabs comics in a given directory starting with 00001.png and working on up.
	
	Parameters:
	
		grab_uri - The URI 'directory' to grab from.
		output_uri - The directory to copy to.
*/
function grabComics( $grab_uri, $output_uri )
{
	$num = 1;

	// create a cURL handle
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_BINARYTRANSFER, TRUE ); // We're grabbing images
	curl_setopt( $ch, CURLOPT_FILETIME, TRUE ); // We want the modify date
	curl_setopt( $ch, CURLOPT_HEADER, FALSE ); // We don't want headers with the output
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE ); // Don't output return

	$curskip = 0;
	while ( true ) { // Break inside
		$filename = sprintf( "./%05d.png", $num );
		curl_setopt( $ch, CURLOPT_URL, $grab_uri . $filename ); // The URL of course
		$ret = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		
		if ( $info[ 'http_code' ] != 200 ) {
			if ( $info[ 'http_code' ] == 404 ) {
				// First try .gif 
				$filename = sprintf( "./%05d.gif", $num );
				curl_setopt( $ch, CURLOPT_URL, $comic . $filename ); // The URL of course
				$ret = curl_exec( $ch );
				$info = curl_getinfo( $ch );

				if ( $info[ 'http_code' ] == 404 && $curSkip <= $max_skip ) {
					$curSkip++;
					continue; // Goto next
				} else { // Else we've hit the end
					echo "<h1>Success</h1><br />\nFetched ${num} comics for ${grab_uri}.<br />\n";
				}
			} 
			else {
				failure( "Received http code " . $info[ 'http_code' ] . " for ${grab_uri}.<br />\n" );
			}
			break;
		}
		
		$fh = fopen( $output_uri . $filename, "w" );
		if ( $fh === FALSE ) {
			failure( "Unable to open output file ${output_uri}${filename}.<br />\n" );
			break;
		}
		
		$bytes = fwrite( $fh, $ret );
		fclose( $fh );
		
		if ( $bytes == FALSE ) {
			failure( "Error writing file ${output_uri}${filename}.<br />\n" );
			break;
		}
	
		$success = touch( $output_uri . $filename, intval( $info[ 'filetime' ] ) ); // Set modified date
		if ( $success === FALSE ) {
			echo "WARNING: Failed to touch file ${output_uri}${filename}.<br />\n";
		}
	
		$num++;
	}	

	// close cURL resource, and free up system resources
	curl_close( $ch );
}

grabComics( "http://www.rhjunior.com/QQSR/Images/", "./imgs/" );
?>
