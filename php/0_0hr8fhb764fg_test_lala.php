<?php
require_once 'config.php';
require_once 'utils.php';
require_once 'sig.php';

// http://eu.battle.net/sc2/en/profile/248533/1/KageyamA/
// http://eu.battle.net/sc2/ru/profile/222461/1/RAZERiNSo/
// http://eu.battle.net/sc2/en/profile/1951456/1/Mdfst/ladder/29186
// http://eu.battle.net/sc2/en/profile/151411/2/SCIIAntonio/
// http://eu.battle.net/sc2/ru/profile/215861/2/Aaron/
// http://eu.battle.net/sc2/ru/profile/74373/2/BVOne/
// http://eu.battle.net/sc2/ru/profile/2175278/1/BRuǂMakka/
// http://eu.battle.net/sc2/en/profile/3391476/1/IlIlIlIlIlIl/
// http://sc2ranks.com
$isSigCreated = Sig::Create( '1', 3391476, 1, 'IlIlIlIlIlIl', 11, 0, 'ru', 'RU', 123 );

// http://us.battle.net/sc2/en/profile/1644048/1/IAmTheWalrus/
//$isSigCreated = $sig->Create( '1', 1644048, 1, 'IAmTheWalrus', 130, 0, 'ru', 'US', 777 );
var_dump( $isSigCreated );
if ( $isSigCreated ) {
	echo '<img src="../1.png">';
}
else {
	echo 'err';
}

echo date( 'H:i', time() );
?>