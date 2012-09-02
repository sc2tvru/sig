<?php
require_once 'config.php';
require_once 'utils.php';
require_once 'db.php';

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

$queryString = 'DELETE FROM '.DB_TABLE_PREFIX.'data WHERE ';

$debugOutput = '';
$doesOldSigExist = false;

foreach ( glob( SIG_BASEDIR.'*.png' ) as $filename ) {
	if ( time() - filemtime( $filename ) > SIG_TTL ) {
		$doesOldSigExist = true;
		// echo "$filename - ".date( 'd M H:i:s', filemtime( $filename ) ). "\n";
		preg_match( "#/([\d]+).png#", $filename, $match );
		
		if ( $match[ 1 ] !== '' ) {
			$queryString .= 'sigId="'.$match[ 1 ].'" OR ';
			$debugOutput .= $match[ 1 ] . ', ';
		}
		@unlink( $filename );
	}
}

if ( $doesOldSigExist ) {
	$queryString = substr( $queryString, 0, strlen( $queryString ) - 4 );
	$queryResult = $db->Query( $queryString );

	if ( $queryResult ) {
		$debugOutput = 'Cleaner delete sigs:' . $debugOutput;
		SaveForDebug( $debugOutput );
	}
}
else {
	SaveForDebug( 'Cleaner doesn\'t found old sigs.' );
}
?>