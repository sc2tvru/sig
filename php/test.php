<?php
require_once 'config.php';
error_reporting( E_ALL );

require_once 'sig.php';
$sig = new Sig;
// http://eu.battle.net/sc2/en/profile/248533/1/KageyamA/
// http://eu.battle.net/sc2/ru/profile/222461/1/RAZERiNSo/
// http://eu.battle.net/sc2/en/profile/1951456/1/Mdfst/ladder/29186
// http://eu.battle.net/sc2/en/profile/151411/2/SCIIAntonio/
$isSigCreated = $sig->Create( '1', 222461, 1, 'RAZERiNSo', 130, 0, 'ru', 'EU', 7777 );

// http://us.battle.net/sc2/en/profile/1644048/1/IAmTheWalrus/
//$isSigCreated = $sig->Create( '1', 1644048, 1, 'IAmTheWalrus', 130, 0, 'ru', 'US', 777 );

if ( $isSigCreated ) {
	echo '<img src="../1.png">';
}
else {
	echo 'err';
}

echo date( 'H:i', time() );
?>