<?php
static $link;
$link = mysql_connect( 'mysql.meg-tech.com', 'rhjunior', 'changeme123' ) or die ("Could not connect to database!");
mysql_select_db( 'rhjunior', $link ) or die ("Could not connect to database!");

function verify_query( $sql, $emptyCheck = TRUE ) {
	global $link;
	$result = mysql_query( $sql, $link );

	if ( !$result ) {
	    echo "Could not successfully run query from DB: " . mysql_error();
	    exit;
	}

	if ( $emptyCheck && mysql_num_rows( $result ) == 0 ) {
	    echo "No rows found!";
	    exit;
	}
	
	return $result;
}

return $link;
?>