<?php
/**
 * генерация баров по крону
 */

// берем параметры из базы
require_once 'config.php';

define( 'START_TIME', GetMicroTime() );

// время, до которого можно запускать генерацию бара
define( 'DEADLINE', START_TIME + TIME_LIMIT - DB_TIME_LIMIT );

require_once 'utils.php';
require_once 'db.php';

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

// для того, чтобы знать, надо ли начинать следующий заход генерации, 
// определяем номер бара, с которого начнем
$queryString = 'SELECT value FROM '.DB_TABLE_PREFIX.'options where name="lastSigId"';
$queryResult = $db->Query( $queryString );

if ( !$queryResult ) {
	exit( 'db err' );
}

if ( $queryResult->num_rows == 0 ) {
	$startSigId = 0;
}
else {
	$row = $queryResult->fetch_assoc();
	$startSigId = intval( $row[ 'value' ] );
}

// и определяем диапазон баров
$queryString = 'SELECT max(sigId) as maxSigId, min(sigId) as minSigId FROM '.DB_TABLE_PREFIX.'data';
$queryResult = $db->Query( $queryString );

if ( !$queryResult ) {
	exit( 'db err' );
}

$row = $queryResult->fetch_assoc();
$maxSigId = $row[ 'maxSigId' ];
$minSigId = $row[ 'minSigId' ];

// если номер вышел за границы, начинаем заново
if ( $startSigId < $minSigId || $startSigId > $maxSigId ) {
	echo "restart\n";
	// на 1 меньше, чтобы захватить 1й
	$startSigId = $minSigId - 1;
}

require_once 'sig.php';
$sigUpdatedCount = 0;
CreateSigBatch( $startSigId, NUM_SIG );

// генерация пачки баров
function CreateSigBatch( $startSigId, $sigCount ) {
	global $db, $sigUpdatedCount;
	
	$queryString = 'SELECT * FROM '.DB_TABLE_PREFIX."data WHERE sigId > $startSigId ORDER BY sigId LIMIT $sigCount";
	$queryResult = $db->Query( $queryString );
	
	if ( $queryResult->num_rows == 0 ) {
		exit;
	}
	
	$lastSigId = 0;
	
	while ( $userSig = $queryResult->fetch_assoc() ) {
		if ( IsTimeEnough() ) {
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
			echo $userSig[ 'sigId' ]. "\n";
			$sigUpdatedCount++;
		}
		else {
			SaveNextNum( $startSigId, $userSig[ 'sigId' ] );
			exit;
		}
		
		$lastSigId = $userSig[ 'sigId' ];
	}
	
	SaveNextNum( $startSigId, $lastSigId );
}


function SaveNextNum( $startSigId, $lastSigId = 0 ) {
	global $sigUpdatedCount, $db;
	
	if ( $lastSigId == 0 ) {
		$lastSigId = $startSigId + $sigUpdatedCount;
	}
	
	$queryString = "UPDATE ".DB_TABLE_PREFIX."options SET value='$lastSigId'
		WHERE name='lastSigId'";
	$result = $db->Query( $queryString );
	
	echo $sigUpdatedCount.' '.date( 'H:i', time() ).' '.$_SERVER[ REMOTE_ADDR ]. ' lastSigId = '. $lastSigId;
}


function IsTimeEnough() {
	$timeDiff = round( DEADLINE - GetMicroTime(), 3 );
	if ( $timeDiff > 0 ) {
		return true;
	}
	else {
		return false;
	}
}


function GetMicroTime() {
	return microtime( true );
}
?>