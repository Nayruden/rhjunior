<?php
error_reporting( E_ALL ); // Since this is just a utility script.
require_once( './database.php' );

$sql = "SELECT value FROM `config` WHERE `key`='extsToTry'";
$result = verify_query( $sql );
$row = mysql_fetch_assoc($result);
$extsToTry = explode( ',', $row[ 'value' ] );
mysql_free_result( $result );
print_r( $extsToTry );

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
	$num = 0;
	$fetched = 0; // How many we actually retreived

	// create a cURL handle
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_BINARYTRANSFER, TRUE ); // We're grabbing images
	curl_setopt( $ch, CURLOPT_FILETIME, TRUE ); // We want the modify date
	curl_setopt( $ch, CURLOPT_HEADER, FALSE ); // We don't want headers with the output
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE ); // Don't output return

	// First, define our vars
	$info = NULL;
	$filename = NULL;
	global $extsToTry;
	global $maxSkip;
	$curSkip = 0;
	
	$sql = "SELECT value FROM `config` WHERE `key`='extsToTry'";
	$result = verify_query( $sql );
	$row = mysql_fetch_assoc($result);
	$extsToTry = explode( ',', $row[ 'value' ] );
	mysql_free_result( $result );
	
	$sql = "SELECT value FROM `config` WHERE `key`='maxSkip'";
	$result = verify_query( $sql );
	$row = mysql_fetch_assoc($result);
	$maxSkip = $row[ 'value' ];
	mysql_free_result( $result );
	
	while ( true ) { // Break inside
		// First we'll check if we have this file already
		$alreadyFetched = FALSE;
		foreach ( $extsToTry as $ext ) {
			$filename = sprintf( './%05d.%s', $num, $ext );
			if ( file_exists( $output_uri . $filename ) ) {
				$alreadyFetched = TRUE;
				break;
			}
		}
		
		if ( $alreadyFetched === TRUE ) {
			$num++;
			$curSkip = 0;
			continue;
		}
		
		// Now to fetch it from the site
		foreach ( $extsToTry as $ext ) {
			$filename = sprintf( './%05d.%s', $num, $ext );
			curl_setopt( $ch, CURLOPT_URL, $grab_uri . $filename ); // The URL of course
			$ret = curl_exec( $ch );
			$info = curl_getinfo( $ch );
			
			if ( $info[ 'http_code' ] == 200 )
				break; // Found it on this ext
		}
		
		if ( $info[ 'http_code' ] == 404 ) {
			if ( $curSkip <= $maxSkip ) {
				$curSkip++;
				$num++;
				continue; // Goto next
			} else { // Else we've hit the end
				$num -= $curSkip + 1;
				echo "<h1>Success</h1><br />\nFetched ${fetched} comics for ${grab_uri}. There are ${num} comics for this series now.<br />\n";
				break;
			}
		} elseif ( $info[ 'http_code' ] != 200 ) {
			failure( "Received http code " . $info[ 'http_code' ] . " for ${grab_uri}.<br />\n" );
			break;			
		}
		
		$curSkip = 0;
		
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
		
		echo "Successfully wrote ${filename}.<br />\n";
	
		$num++;
		$fetched++;
	}	

	// close cURL resource, and free up system resources
	curl_close( $ch );
}

grabComics( "http://www.rhjunior.com/QQSR/Images/", "./QQSR/" );
//grabComics( "http://rhjunior.com/totq/Images/", "./totq/" );
//grabComics( "http://rhjunior.com/NT/Images/", "./NT/" );
//grabComics( "http://rhjunior.com/GH/Images/", "./GH/" );
//grabComics( "http://rhjunior.com/TH/Images/", "./TH/" );
//grabComics( "http://rhjunior.com/FoH/Images/", "./FoH/" );
//grabComics( "http://rhjunior.com/CC/Images/", "./CC/" );

mysql_close( $link );
?>
