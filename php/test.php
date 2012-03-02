<?php
require_once 'config.php';
require_once 'utils.php';
require_once 'sig.php';

// http://eu.battle.net/sc2/en/profile/248533/1/KageyamA/
// http://eu.battle.net/sc2/ru/profile/222461/1/RAZERiNSo/
// http://eu.battle.net/sc2/en/profile/1951456/1/Mdfst/ladder/29186
// http://eu.battle.net/sc2/en/profile/151411/2/SCIIAntonio/
// http://eu.battle.net/sc2/ru/profile/215861/2/Aaron/
$isSigCreated = Sig::Create( '5024', 74373, 2, 'BVOne', 11, 2, 'ru', 'RU', 293 );

// http://us.battle.net/sc2/en/profile/1644048/1/IAmTheWalrus/
//$isSigCreated = $sig->Create( '1', 1644048, 1, 'IAmTheWalrus', 130, 0, 'ru', 'US', 777 );
var_dump( $isSigCreated );
if ( $isSigCreated ) {
	echo '<img src="../5024.png">';
}
else {
	echo 'err';
}

echo date( 'H:i', time() );
?>