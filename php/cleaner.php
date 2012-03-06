<?php
require_once 'config.php';
require_once 'utils.php';
require_once 'db.php';

// время жизни необновляемого бара
define( 'SIG_TTL', 259200 );

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

$queryString = 'DELETE FROM '.DB_TABLE_PREFIX.'data WHERE ';

foreach ( glob( SIG_BASEDIR.'*.png' ) as $filename ) {
	if ( time() - filemtime( $filename ) > SIG_TTL ) {
		//echo "$filename - ".date( 'd M H:i:s', filemtime( $filename ) ). "\n";
		preg_match( "#/([\d]+).png#", $filename, $match );
		
		if ( $match[ 1 ] !== '' ) {
			$queryString .= 'sigId="'.$match[ 1 ].'" OR ';
		}
		@unlink( $filename );
	}
}

$queryString = substr( $queryString, 0, strlen( $queryString ) - 4 );
// echo $queryString;
echo date( 'd M H:i:s', time() );

$queryResult = $db->Query( $queryString );
?>