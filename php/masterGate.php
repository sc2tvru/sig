<?php
/**
 * шлюз для мастера для создания картинки
 * потом картинки генерируются по крону скриптом sigCron.php
 * @author shr, forshr@gmail.com
 */

//error_reporting( 0 );

// проверяем REFERER - обращение должно быть с адреса мастера
// TODO: REFERER легко подделать, хорошо бы добавить еще какую-то защиту
// иначе могут просто сделать DoS через огромное число запросов на создание картинки
$posRefferer = strpos( $_SERVER[ 'HTTP_REFERER' ], 'http://sc2tv.ru/sig/' );
if ( $posRefferer === false ) {
	exit;
}

$playerId =  intval( $_GET[ 'playerId' ] );
$bnetServerNum = intval( $_GET[ 'bnetServerNum' ] );
$playerAccount = preg_replace( '/([^\w\dа-яА-Я]ǂ)/ui', '', urldecode( $_GET[ 'playerAccount' ] ) );

$sigBackgroundIndex = intval( $_GET[ 'sigBackgroundIndex' ] );

preg_match( '/(1v1|2v2|3v3|4v4)/i', $_GET[ 'playerStatsType' ], $match );
$playerStatsType = $match[ 1 ];

switch( $playerStatsType ) {
	case '1v1':
		$playerStatsIndex = 0;
	break;
	case '2v2':
		$playerStatsIndex = 1;
	break;
	case '3v3':
		$playerStatsIndex = 2;
	break;
	case '4v4':
		$playerStatsIndex = 3;
	break;

	default:
		$playerStatsIndex = 0;
	break;
}

switch( $_GET[ 'lang' ] ) {
	case 'ru':
		$lang = 'ru';
	break;
	
	case 'en':
		$lang = 'en';
	break;
	
	default:
		$lang = 'ru';
	break;
}

switch( $_GET[ 'region' ] ) {
	case 'EU':
		$region = 'EU';
	break;
	
	case 'RU':
		$region = 'RU';
	break;
	
	case 'US':
		$region = 'US';
	break;
	
	case 'KR':
		$region = 'KR';
	break;
	
	default:
		$region = 0;
	break;
}

$characterCode = intval( $_GET[ 'characterCode' ] );

require_once 'config.php';
require_once 'utils.php';
require_once 'db.php';

$db = new MySqlDb( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );

// узнаем sigId
// сначала нужно проверить, есть ли такой игрок в базе
$queryString = 'SELECT sigId FROM '.DB_TABLE_PREFIX.'data WHERE playerId="'.$playerId.'"';
$queryResult = $db->Query( $queryString );

if ( $queryResult ) {
	$row = $queryResult->fetch_assoc();
	$sigId = $row[ 'sigId' ];
	$isPlayerNew = false;
}
else {
	exit;
}

if ( !$sigId ) {
	$isPlayerNew = true;
	$queryString = 'SELECT MAX(sigId) as maxSigId FROM '.DB_TABLE_PREFIX.'data';
	$queryResult = $db->Query( $queryString );

	if ( $queryResult ) {
		$row = $queryResult->fetch_assoc();
		$sigId = $row[ 'maxSigId' ] + 1;
	}
	else {
		exit;
	}
}

if ( !( $playerId && $bnetServerNum && $playerAccount && $sigBackgroundIndex &&
	isset( $playerStatsIndex ) ) ) {
	exit( 'err' );
}

require_once 'sig.php';

$isSigCreated = Sig::Create( $sigId, $playerId, $bnetServerNum, $playerAccount,
	$sigBackgroundIndex, $playerStatsIndex, $lang, $region, $characterCode );

if ( $isSigCreated ) {
	// сохраняем параметры в базу для sigCron.php
	if ( $isPlayerNew ) {
		$queryString = "INSERT INTO ".DB_TABLE_PREFIX."data
			( sigId, playerId, bnetServerNum, playerAccount, sigBackgroundIndex,
			playerStatsIndex, lang, region, characterCode )
			values(	'$sigId', '$playerId', '$bnetServerNum', '$playerAccount',
			'$sigBackgroundIndex', '$playerStatsIndex', '$lang', '$region', '$characterCode' )";
	}
	else {
		$queryString = "UPDATE ".DB_TABLE_PREFIX."data SET
			bnetServerNum='$bnetServerNum', playerAccount='$playerAccount',
			sigBackgroundIndex='$sigBackgroundIndex', playerStatsIndex='$playerStatsIndex',
			lang='$lang', region='$region', characterCode='$characterCode'
			WHERE sigId='$sigId'";
	}
	
	$result = $db->Query( $queryString );
	
	if ( $result ) {
		echo 'ok;'.$sigId;
	}
	else {
		exit( 'err' );
	}
}
else {
	exit( 'err' );
}
?>