<?php
if ( preg_match( '/'.basename(__FILE__).'/i', $_SERVER['PHP_SELF'] ) ) {
	exit( 'access denied' );
}

require_once 'maincore.php';

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

/**
 * класс для работы с MySQL
 * редакция от 2010-09-24 
 * @author shr, forshr@gmail.com
 *
 */
class MySqlDb {
	var $mysqli;
	
	function MySqlDb( $dbHost, $dbName, $dbUser, $dbPassword ) {
		$this->mysqli = new mysqli( $dbHost, $dbUser, $dbPassword, $dbName );
		
		if ( !$this->mysqli->connect_error ) {
			$this->mysqli->query( 'SET NAMES utf8' );
			return $this->mysqli;
		}
		else {
			ErrorHandler( ' database connection error', __FILE__, __LINE__ );
		}
	}


	function Query( $queryString ) {
		//echo $queryString.'<br>';
		$result = $this->mysqli->query( $queryString );
		if ( $result ) {
			return $result;
		}
		else {
			ErrorHandler( ' database query error', __FILE__, __LINE__ );
			return false;
		}
	}


	function PrepareParams() {
		$params = func_get_args();
		if ( get_magic_quotes_gpc() ) {
			$params = array_map( 'stripslashes', $params );
		}
		$params = array_map( 'phpentities', $params );
		
		$cleanParams = array();
		foreach ( $params as $param ) {
			$cleanParams[] = $this->mysqli->real_escape_string( $param );
		}
		 
		return $cleanParams;
	}
}
?>