<?php
/**
 * вспомогательные функции
*/

if ( LOG_ERRORS ) {
	set_error_handler( 'ErrorHandler' );
}


function ErrorHandler( $errno = '', $errstr = '', $errfile = '', $errline = ''  ) {
	$logFile = fopen( ERROR_FILE, 'a' );
	
	if ( flock( $logFile, LOCK_EX | LOCK_NB ) ) {
		$out = date( 'd M H:i:s', time() ) ."\n $errfile, line $errline, code $errno, $errstr\n\n";
		fwrite( $logFile, $out );
		fflush( $logFile );
		flock( $logFile, LOCK_UN );
	}
	
	fclose( $logFile );
	
	return false;
}


function SaveForDebug( $str ) {
	$logFile = fopen( DEBUG_FILE, 'a' );
	
	if ( flock( $logFile, LOCK_EX | LOCK_NB ) ) {
		$str = date( 'd M H:i:s', CURRENT_TIME ). ", debug: $str";
		fwrite( $logFile, $str. "\n\n" );
		fflush( $logFile );
		flock( $logFile, LOCK_UN ); 
	}
	
	fclose( $logFile );
}
?>