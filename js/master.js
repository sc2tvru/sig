masterLink = '';
$( document ).ready( function(){

// navigation
$( '.next-step' ).click( function(){
	btnPrevNext = $( this ).parents( 'div' );
	stepId = btnPrevNext.parents( 'div' ).attr( 'id' );
	stepId = parseInt( stepId.substr( stepId.indexOf( 'step' ) + 4 ) );
	
	
	switch ( stepId ) {
		case 1:
			bnetLinkStr = $( '#bnetLink' ).attr( 'value' );
			if ( bnetLinkStr != null && bnetLinkStr.length > 36 ) {
				arr = bnetLinkStr.split( '/' );
				masterLink = arr[6] + '_' + arr[7] + '_' + arr[8] + '_1_1v1';
				$( '#sig-back-preview' ).attr( 'src', 'img/master/not_selected.png' );
			}
			else {
				alert( "Вы не заполнили поле или заполнили неверно!" );
				return false;
			}
		break;
		
		case 3:
			if ( confirm( 'Вы уверены, что указали все параметры правильно?' ) == true ) {
				statsType = $( '#stats-type' ).val();
				playerLang = $( '#lang' ).val();
				playerRegion = $( '#region' ).val();
				characterCode = $( '#character-code' ).attr( 'value' );
				arr = masterLink.split( '_' );
				dataAjax = 'playerId=' + arr[0] + '&bnetServerNum=' + arr[1] + '&playerAccount=' + arr[2] + '&sigBackgroundIndex=' + arr[3] + '&playerStatsType=' + statsType + '&lang=' + playerLang + '&region=' + playerRegion + '&characterCode=' + characterCode;
				$.ajax({
					url: 'php/masterGate.php',
					data: dataAjax,
					cache: false,
					success: function( msg ){
						answer = msg.split( ';' );
						if ( answer[0] == 'ok' ) {
							sigPreviewSrc = answer[1];
							masterLink = 'http://sc2tv.ru/sig/' + sigPreviewSrc + '.png';
							$( '#sig-preview').attr( 'src', masterLink );
							$( '#sig-preview').css( 'display', 'block' );
							$( '#masterLinkInputImg').attr( 'value', masterLink );
							$( '#masterLinkInputBb').attr( 'value', '[url=http://sc2tv.ru/][img]' + masterLink + '[/img][/url]' );
							$( '#masterLinkInputHtml').attr( 'value', '<a href="http://sc2tv.ru/"><img src="' + masterLink + '"></a>' );
						}
						else {
							alert( 'Проверьте правильность введенных данных и попробуйте повторить попытку позже' );
						}
					}
				});
			}
			else {
				return false;
			}
		break;
	}
	prevStepId = '#step' + stepId;
	stepId += 1;
	$( '#watch-step-image' ).attr( 'src', 'img/master/step_' + (stepId) + '.png' );
	stepId = '#step' + stepId;
	$( prevStepId ).css( 'display','none' );
	$( stepId ).css( 'display','block' );
});


$( '.prev-step' ).click( function(){
	btnPrevNext = $( this ).parents( 'div' );
	stepId = btnPrevNext.parents( 'div' ).attr( 'id' );
	stepId = parseInt( stepId.substr( stepId.indexOf( 'step' ) + 4 ) );

  $( '#watch-step-image' ).attr( 'src', 'img/master/step_' + (stepId-1) + '.png' );
	prevStepId = '#step' + stepId;
	stepId -= 1;
	stepId = '#step' + stepId;
	$( prevStepId ).css( 'display', 'none' );
	$( stepId ).css( 'display', 'block' );
});


function GetContentIdByRaceTabId ( raceTabId ) {
	contentId = raceTabId.substr( 0, raceTabId.length - 3 ) + 'content';
	return contentId;
}


// race tabs
$( 'ul.race-tabs span' ).click( function HandleTab(){
	activeTab = $( '.active-race-tab' );
	activeContentId = '#' + GetContentIdByRaceTabId( activeTab.attr( 'id' ) );
	$( activeContentId ).css( 'display','none');
	activeTab.attr( 'class', '' );
	nextContentId = '#' + GetContentIdByRaceTabId( $( this ).attr( 'id' ) );
	$( nextContentId ).css( 'display','block');
	$( this ).attr( 'class', 'active-race-tab' );
	
	$( 'ul.race-tabs span' ).unbind('click', HandleTab );
	$( 'ul.race-tabs span' ).click(HandleTab);
} );

// background select
$( 'div.scroll-pane img' ).click( function(){
	backSrc = $( this ).attr( 'src' );
	$( '#sig-back-preview' ).attr( 'src', backSrc );
	backStartPos = backSrc.indexOf( 'med_back_' ) + 9;
	backEndPos = backSrc.indexOf( '.png', backStartPos );
	backIndex = backSrc.substr( backStartPos, backEndPos - backStartPos );
	arr = masterLink.split( '_' );
	masterLink = arr[0] + '_' + arr[1] + '_' + arr[2] + '_' + backIndex + '_' + arr[4];
} );


$( '.start-again' ).click( function(){
	masterLink = '';
	$( '#watch-step-image' ).attr( 'src', 'img/master/step_1.png' );
	$( '#bnetLink' ).attr( 'value', '' );
	$( '#sig-back-preview' ).attr( 'src', 'img/master/not_selected.png' );
	$(this).parents('div').css( 'display','none');
	$( '#step1' ).css( 'display','block');
} );
});