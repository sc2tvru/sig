<?php
/**
 * генерация баров по крону
 */

// берем параметры из базы
require_once 'config.php';

define( 'START_TIME', getMicroTime() );

// время, до которого можно запускать генерацию бара
define( 'DEADLINE', START_TIME + TIME_LIMIT - DB_TIME_LIMIT );

require_once 'utils.php';
require_once 'db.php';

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

// для того, чтобы знать, надо ли начинать следующий заход генерации, 
// определяем номер бара, с которого начнем
$queryString = 'SELECT value FROM '.DB_TABLE_PREFIX.'options where name="nextSigNum"';
$queryResult = $db->Query( $queryString );

if ( !$queryResult ) {
	exit( 'db err' );
}

if ( $queryResult->num_rows == 0 ) {
	$startNum = 0;
}
else {
	$row = $queryResult->fetch_assoc();
	$startNum = intval( $row[ 'value' ] );
}

// и определяем кол-во баров
$queryString = 'SELECT COUNT(playerId) as count FROM '.DB_TABLE_PREFIX.'data';
$queryResult = $db->Query( $queryString );

if ( !$queryResult ) {
	exit( 'db err' );
}

$row = $queryResult->fetch_assoc();
$totalSigCount = $row[ 'count' ];

// если номер вышел за границы, начинаем заново
if ( $startNum >= $totalSigCount || $startNum < 1000 ) {
	$startNum = 1000;
}

require_once 'sig.php';
$sigUpdatedCount = 0;
CreateSigBatch( $startNum, NUM_SIG );

// генерация пачки баров
function CreateSigBatch( $startNum, $sigCount ) {
	global $db, $sigUpdatedCount;
	
	$queryString = 'SELECT * FROM '.DB_TABLE_PREFIX."data LIMIT $startNum, $sigCount";
	$queryResult = $db->Query( $queryString );
	
	if ( $queryResult->num_rows == 0 ) {
		exit;
	}
	
	while ( $userSig = $queryResult->fetch_assoc() ) {
		if ( isTimeEnough() ) {
			$isSigCreated = Sig::Create(
				$userSig[ 'sigId' ],
				$userSig[ 'playerId' ],
				$userSig[ 'bnetServerNum' ],
				$userSig[ 'playerAccount' ],
				$userSig[ 'sigBackgroundIndex' ],
				$userSig[ 'playerStatsIndex' ],
				$userSig[ 'lang' ],
				$userSig[ 'region' ],
				$userSig[ 'characterCode' ]
			);
			$sigUpdatedCount++;
		}
		else {
			SaveNextNum();
			exit;
		}
	}
	
	SaveNextNum();
}


function SaveNextNum() {
	global $startNum, $sigUpdatedCount, $db;
	$startNum += $sigUpdatedCount;
	$queryString = "UPDATE ".DB_TABLE_PREFIX."options SET value='$startNum'
		WHERE name='nextSigNum'";
	$result = $db->Query( $queryString );
	echo $sigUpdatedCount.' '.date( 'H:i', time() ).' '.$_SERVER[ REMOTE_ADDR ]. 'nextSigNum = '. $startNum;
}


function isTimeEnough() {
	$timeDiff = round( DEADLINE - getMicroTime(), 3 );
	if ( $timeDiff > 0 ) {
		return true;
	}
	else {
		return false;
	}
}


function getMicroTime() {
	return microtime( true );
}
?>