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
		$serverInfo = var_export( $_SERVER, true );
		
		$out = date( 'd M H:i:s', time() ).' - ip '.$_SERVER[ 'REMOTE_ADDR' ]
			.' - ref '.$_SERVER[ 'HTTP_REFERER' ]."\n $errfile, line $errline, code $errno, $errstr\n\n$serverInfo\n\n";
		
		fwrite( $logFile, $out );
		fflush( $logFile );
		flock( $logFile, LOCK_UN );
	}
	
	fclose( $logFile );
	
	return false;
}
?>