<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Создание таблиц в базе данных для баров со статистикой в StarCraft 2</title>
</head>
<body>
<?php
/*
флаг, указывающий на то, созданы ли таблицы для работы
после выполнения скрипта установить в true, сам файл переименовать
в .ht_create_db_tables.php_
*/
define( 'DB_READY', true );

if ( DB_READY ) {
	die( 'Таблицы уже были созданы. Если это новая установка, измените флаг DB_READY на false</body></html>' );
}

require_once 'config.php';
require_once 'db.php';

$querys[] = 'CREATE TABLE '.DB_TABLE_PREFIX.'data (
sigId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
playerId INT UNSIGNED NOT NULL UNIQUE,
bnetServerNum TINYINT UNSIGNED NOT NULL,
playerAccount VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
sigBackgroundIndex SMALLINT UNSIGNED NOT NULL,
playerStatsIndex TINYINT UNSIGNED NOT NULL,
lang CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "ru",
region CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci,
characterCode INT UNSIGNED )';

$querys[] = 'CREATE TABLE '.DB_TABLE_PREFIX.'options (
name VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
value VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
PRIMARY KEY( name ) )';

$querys[] = 'INSERT INTO '.DB_TABLE_PREFIX.'options values(
"nextSigNum", "0" )';

foreach ( $querys as $query_string ) {
	$queryResult = $db -> Query( $query_string );
	if ( $queryResult ) {
		echo '<font color ="green">';
	}
	else {
		echo '<font color ="red">';
	}
	echo $query_string.'</font><br><br>';
}

?>
</body>
</html>