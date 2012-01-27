<?php
/**
 * Функции, которые могут быть использованы везде
 * редакция 2011-03-06
 * автор shr, forshr@gmail.com
*/

if ( preg_match( '/'.basename(__FILE__).'/i', $_SERVER['PHP_SELF'] ) ) {
	exit( 'access denied' );
}

if ( LOG_ERRORS ) {
	$ErrorString='';
	$shrErrorHandler = set_error_handler( 'ErrorHandler' );
}
else {
	error_reporting( E_ALL );
}


function ErrorHandler( $errstr ='', $errfile = '', $errline = '', $errno = '' ) {
	global $ErrorString;

	$ErrorString .= date( 'd M H:i:s', time() ).' - ip '.$_SERVER[ 'REMOTE_ADDR' ];
	$ErrorString .= ' - ref '.$_SERVER[ 'HTTP_REFERER' ]." - $errfile line $errline; $errstr; code $errno\n\n";

	if ( strlen( $ErrorString ) > 0 ) {
		$log_file = fopen( 'error_log.txt', 'a+' );
		fwrite( $log_file, $ErrorString );
		fclose( $log_file );
	}
}

// htmlentities слишком агрессивно правит русские символы, поэтому используем это
function phpentities( $text ) {
	$search = array('&', '"', "'", '\\', '<', '>' );
	$replace = array('&amp;', '&quot;', '&#39;', '&#92;', '&lt;', '&gt;' );
	$text = str_replace($search, $replace, $text);
	return $text;
}
/*
function SaveForDebug( $str ) {
	$logFile = fopen( 'debug.txt', 'a' );
	
	if ( flock( $logFile, LOCK_EX | LOCK_NB ) ) {
		$str = date( 'd M H:i:s', time() )
			. ' - '. $_SERVER[ 'REMOTE_ADDR' ]
			. ' - ' . $_SERVER[ 'HTTP_USER_AGENT' ]
			. ' - ref '. $_SERVER[ 'HTTP_REFERER' ]. "\n" . $str;
		
		fwrite( $logFile, $str. "\n\n" );
		fflush( $logFile );
		flock( $logFile, LOCK_UN ); 
	}
	
	fclose( $logFile );
}
*/
?>