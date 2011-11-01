<?php
/*
 * Примеры бнет акков
 * http://eu.battle.net/sc2/ru/profile/1997770/1/greed/
 * http://eu.battle.net/sc2/en/profile/248533/1/KageyamA/
 */

/**
 * Генерация картинок со статистикой StarCraft 2
 * @author MyIE
 * @author shr, forshr@gmail.com
 * редакция 2011-11-14
 *
 */
// Ачивки
define( 'FONT_ACHIEVEMENTS', 'eurostile_ext_med.otf' );
// Статс и ранк
define( 'FONT_STATS_RANK', 'calibri.ttf' );
// размер шрифта текста
define( 'TEXT_FONT_SIZE', 8 );
// отношение стороны нашего шаблона портретов к тому, что на bnet
define( 'PORTRAIT_KOEFICENT', 23/45 );
putenv( 'GDFONTPATH=' . realpath( '.' ) );

if ( !extension_loaded( 'gd' ) ) {
	exit( 'Sig: image processing library not loaded' );
}
		
class Sig {
	
	/**
	 * @param int $sigId
	 * @param int $playerId
	 * @param int $bnetServerNum
	 * @param string $playerAccount
	 * @param int $sigBackgroundIndex
	 * @param int $playerStatsIndex
	 * @param string $lang
	 * @param string $region
	 * @param int $characterCode
	 * @return boolean
	 */
	public function Create( $sigId, $playerId, $bnetServerNum, $playerAccount, $sigBackgroundIndex=1,
		$playerStatsIndex, $lang, $region, $characterCode ) {
		
		// ник
		preg_match( '/[^a-z0-9\-]*/ui', $playerAccount, $match );
		if ( $match[ 0 ] ) {
			// с русскими символами
			$fontAccount = 'calibri.ttf';
		}
		else {
			$fontAccount = 'eurostile_ext_med.otf';
		}
		// echo $playerAccount.' '.$fontAccount."\n";
		
		// языки \ локали
		$locale[ 'ru' ][ 'notRanked' ] = ' НЕТ РЕЙТИНГА';
		$locale[ 'ru' ][ 'place' ] = 'МЕСТО';
		$locale[ 'ru' ][ 'points' ] = 'ОЧКИ';
		$locale[ 'ru' ][ 'region' ] = 'РЕГИОН';
		$locale[ 'ru' ][ 'wins' ] = 'ПОБЕД';
		
		$locale[ 'en' ][ 'notRanked' ] = ' NOT YET RANKED';
		$locale[ 'en' ][ 'place' ] = 'PLACE';
		$locale[ 'en' ][ 'points' ] = 'POINTS';
		$locale[ 'en' ][ 'region' ] = 'REGION';
		$locale[ 'en' ][ 'wins' ] = 'WINS';
		
		if ( !( $lang == 'ru' || $lang == 'en' ) ) {
			$lang = 'ru';
		}
		
		$bnetSubDomain = 'eu';
		
		if ( $region === 'US' || $region === 'KR' ) {
			$bnetSubDomain = strtolower( $region );
		}
		elseif ( $region == 0 ) {
			$region = false;
		}
		
		$sigPath = SIG_BASEDIR.$sigId.'.png';
		
		require_once 'network.php';
		
		$playerAccountUrl = urlencode( $playerAccount );
		
		$data = Network::GetHTTPData(
			"http://$bnetSubDomain.battle.net/sc2/en/profile/$playerId/$bnetServerNum/$playerAccountUrl/",
			'id="portrait',
			'class="module-right'
		);
		
		if ( !$data ) {
			return false;
		}

		switch( $playerStatsIndex ) {
			case 0:
				$playerStatsType = '1v1';
				break;
			
			case 1:
				$playerStatsType = '2v2';
				break;
			
			case 2:
				$playerStatsType = '3v3';
				break;
			
			case 3:
				$playerStatsType = '4v4';
				break;

			default:
				$playerStatsType = '1v1';
				break;
		}
		
		if ( $characterCode ) {
			$regionX = 133;
		}
		else {
			$regionX = 170;
		}

		if ( $characterCode || $region ) {
			$playerAccountY = 19;
		}
		else {
			$playerAccountY = 24;
		}
		
		// получаем число ачивок
		preg_match( '/h3>([\d]*)</si', $data, $match );
		$playerAchievements = $match[ 1 ];

		// определяем координаты портрета персонажа
		if ( preg_match( "#portraits/([-\d]+).jpg.*?'\) ([-\d]+)px ([-\d]+)px no#si",
			$data, $match ) ) {
			// из какой заготовки будем брать
			$portraitImg = 'portraits-'.$match[ 1 ];

			// координаты, откуда будем брать
			$portraitX = abs( $match[ 2 ]*PORTRAIT_KOEFICENT );
			$portraitY = abs( $match[ 3 ]*PORTRAIT_KOEFICENT );
		}

		// место
		$isPlayerHasRank = true;
		preg_match_all(
			'#best-team-'.( $playerStatsIndex + 1 ).'.*?Highest Ranked in '
			.$playerStatsType.'.*?Rank:</strong> ([\d]+)#si',
			$data,
			$match );
		
		if (  $match[ 1 ][ 0 ] ) {
			$playerRank = $locale[ $lang ][ 'place' ].': '.$match[ 1 ][ 0 ];
			$tempPlayerData = $match[ 0 ][ 0 ];
		}
		else {
			$playerStats = '  '.$playerStatsType.$locale[ $lang ][ 'notRanked' ];
			$leagueImg = 'none';
			$isPlayerHasRank = false;
		}
		
		// раса
		if ( preg_match( '|race-([\w]+)">|si', $data, $match) ) {
			$playerRace = $match[ 1 ];
		}
		
		if ( $isPlayerHasRank &&
			preg_match_all( '|<a href="([^"]+)">|si', $tempPlayerData, $match ) ) {
			$data = Network::GetHTTPData(
				'http://' . $bnetSubDomain . '.battle.net'.$match[ 1 ][ 0 ],
				'<head>',
				'id="current-rank".*?tr class="row2"');
			
			if ( !$data ) {
				return false;
			}
			
			// лига
			if ( preg_match_all( '|<title>'.$playerStatsType.' ([\w]+) |si',
				$data, $match ) ) {
				$leagueImg = strtolower( $match[ 1 ][ 0 ] );
			}
			else {
				$leagueImg = 'none';
			}
			
			preg_match( '|id="current-rank"(.*?)tr class|si', $data, $match );
			$data = $match[ 1 ];
			
			if ( preg_match_all( '|<td class="align-center">([\d]+)</td>|si', $data, $match ) ){
				$playerPoints = $locale[ $lang ][ 'points' ].': '.$match[ 1 ][ 0 ];
				
				$wins = $match[ 1 ][ 1 ];
				
				if ( $match[ 1] [ 2 ] ) {
					$lose = $match[ 1 ][ 2 ];
					$playerStats = "$wins / $lose";
					
					$playerWinRate = round( $wins*100/( $wins + $lose ) ).' %';
				}
				else {
					$playerStats = $locale[ $lang ][ 'wins' ].': '.$wins;
				}
			}
		}
		
		$playerImgResource = imagecreatetruecolor( 358, 68 );

		// берем шаблон
		$templateImg = imagecreatefrompng( SIG_BASEDIR.'img/sig/med_back_'.$sigBackgroundIndex.'.png' );

		// белый цвет по контуру становится прозрачным
		$transparentColor = imagecolorallocate( $playerImgResource, 255, 255, 255 );
		imagecolortransparent( $playerImgResource, $transparentColor );

		// берем портрет
		$portrait = imagecreatefrompng( SIG_BASEDIR.'img/sig/'.$portraitImg.'.png' );
		
		// лигу
		$league = imagecreatefrompng( SIG_BASEDIR.'img/sig/leg_'.$leagueImg.'.png' );
		// расу
		$raceTemplateImg = imagecreatefrompng( SIG_BASEDIR."img/sig/race_$playerRace.png");

		// выбираем подходящий цвет текста и фон для bnetId
		list( $textColor, $bnetIdTpl ) = Sig::SelectTextColorAndBnetIdTplByBackgroundIndex(
			$templateImg, $sigBackgroundIndex );
		// выводим все
		
		// первая строка, первый блок
    	imagettftext( $templateImg, TEXT_FONT_SIZE, 0, 101, 46, $textColor, FONT_STATS_RANK,
    		$playerRank );
    	// первая строка, второй блок
    	imagettftext( $templateImg, TEXT_FONT_SIZE, 0, 162, 46, $textColor, FONT_STATS_RANK,
    		$playerPoints );
    		
    	// вторая строка, первый блок
    	imagettftext( $templateImg, TEXT_FONT_SIZE, 0, 101, 57, $textColor, FONT_STATS_RANK,
    		$playerStats );
    	// вторая строка, второй блок
		imagettftext( $templateImg, TEXT_FONT_SIZE, 0, 162, 57, $textColor, FONT_STATS_RANK,
    		$playerWinRate );
		
		$box_nik = imagettfbbox( 11, 0, $fontAccount, $playerAccount );

		// 237 - 12 - $box_nik[ 2 ] = 225 - $box_nik[ 2 ]
		imagettftext( $templateImg, 11, 0, 223 - $box_nik[ 2 ], $playerAccountY, $textColor,
			$fontAccount, $playerAccount );

		imagettftext( $templateImg, 9, 0, 302, 19, $textColor, FONT_ACHIEVEMENTS,
			$playerAchievements );
		imagettftext( $templateImg, 9, 0, 320, 51, $textColor, FONT_ACHIEVEMENTS,
			$playerStatsType );
		
		if ( $characterCode ) {
			imagettftext( $templateImg, 8, 0, 191, 31, $textColor, FONT_STATS_RANK,
				' ID: '.$characterCode );
		}

		if ( $region ) {
			imagettftext( $templateImg, 8, 0, $regionX, 31, $textColor, FONT_STATS_RANK,
				$locale[ $lang ][ 'region' ].': '.$region );
		}

		imagecopyresampled( $playerImgResource, $templateImg, 0, 0, 0, 0, 358, 68,
			imagesx( $templateImg ), imagesy( $templateImg ) );
		imagecopyresampled( $playerImgResource, $portrait, 237, 12, $portraitX, $portraitY,
			46, 46, 46, 46 );
		imagecopyresampled( $playerImgResource, $league, 292, 31, 0, 0, 26, 28, 26, 28);
		imagecopyresampled( $playerImgResource, $raceTemplateImg, 219, 43, 0, 0, 18, 18,
			18, 18 );
		
		imagepng( $playerImgResource, $sigPath );

		// не забываем освобождать за собой память
		imagedestroy( $playerImgResource );
		imagedestroy( $templateImg );
		imagedestroy( $portrait );
		imagedestroy( $league );
		imagedestroy( $raceTemplateImg );
		
		return true;
	}


	// выбор соответствующего цвета для фоновой картинки
	private function SelectTextColorAndBnetIdTplByBackgroundIndex( $imgResource, $backgroundIndex ) {
		if ( 0 < $backgroundIndex && $backgroundIndex <= 25 ||
			101 <= $backgroundIndex && $backgroundIndex <= 110 ) {
			$red = 70;
			$green = 250;
			$blue = 133;
			$bnetIdTpl = 't';
		}
		elseif ( 26 <= $backgroundIndex && $backgroundIndex <= 50 ||
			111 <= $backgroundIndex && $backgroundIndex <= 120 ) {
			$red = 70;
			$green = 200;
			$blue = 250;
			$bnetIdTpl = 'p';
		}
		elseif ( 51 <= $backgroundIndex && $backgroundIndex <= 75 ||
			121 <= $backgroundIndex && $backgroundIndex <= 130 ) {
			$red = 250;
			$green = 160;
			$blue = 70;
			$bnetIdTpl = 'z';
		}
		elseif ( 76 <= $backgroundIndex && $backgroundIndex <= 100 ) {
			$red = 200;
			$green = 200;
			$blue = 200;
			$bnetIdTpl = 'r';
		}
		else {
			$red = 255;
			$green = 255;
			$blue = 255;
			$bnetIdTpl = 'r';
		}
		
		$color = imagecolorallocate( $imgResource, $red, $green, $blue );
		return array( $color, $bnetIdTpl );
	}
}
?>