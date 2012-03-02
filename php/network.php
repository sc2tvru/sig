<?php
/**
 * код для работы с сетью
 * редакция от 2011-04-13
 * @author shr, forshr@gmail.com
 *
 */

if ( strpos( $_SERVER['PHP_SELF'], basename(__FILE__) ) ) {
	exit( 'access denied' );
}

// время ожидания ответа от сервера
define( 'REQUEST_TIMEOUT', 30 );

// максимальное время чтения данных с сервера
define( 'GET_DATA_TIMEOUT', 30 );

// размер блока данных для чтения
define( 'DATA_BLOCK_SIZE', 8192 );
		
class Network {
	
	/**
	 * получение данных между $startSeparator и $endSeparator
	 * @param string $uri
	 * @param string $startSeparator
	 * @param string $endSeparator
	 * @param string $cookie
	 * @return string
	 */
	public static function GetHTTPData( $uri, $startSeparator ='', $endSeparator = '',
		$cookie = '' ) {

		$url_info = @parse_url( $uri );
		$domain = $url_info[ 'host' ];
		$path = $url_info[ 'path' ];
		
		if ( isset( $url_info[ 'query' ] ) ) {
		 $path .= '?' . $url_info[ 'query' ];
		}
		
		if ( isset( $url_info[ 'port' ] ) && $url_info[ 'port' ] > 0 ) {
		 $port = $url_info[ 'port' ];
		}
		else {
			$port = 80;
		}

		$socket = @fsockopen( $domain, $port, $errno, $errstr, REQUEST_TIMEOUT );

		if ( !$socket ) {
			return false;
		}

		// запрос
		$request = "GET $path HTTP/1.0\r\n"
				 . "Host: $domain\r\n"
				 . "User-Agent: sc2tv.ru grabber by shr\r\n"
				 . "Referer: $domain\r\n";

		if ( $cookie ) {
			$request .= "Cookie: $cookie\r\n";
		}

		$request .= "Connection: close\r\n\r\n";

		// echo $request;

		fputs( $socket, $request );

		stream_set_blocking( $socket, true );
		stream_set_timeout( $socket, REQUEST_TIMEOUT );

		// читаем ответ
		$bufer = '';
		$data = '';

		// готовим разделители для использования
		$startSeparator = Network::RegExpPrepare( $startSeparator );
		$endSeparator = Network::RegExpPrepare( $endSeparator );
		
		$startSeparatorFound = false;

		$deadLineTime = time() + GET_DATA_TIMEOUT;

		// читаем, пока не найдем разделитель или конец данных / таймаут
		do {
			$info = stream_get_meta_data( $socket );
			if ( $info[ 'timed_out' ] || time() > $deadLineTime ) {
				break;
			}
				
			$bufer = fgets( $socket, DATA_BLOCK_SIZE );

			if ( $startSeparator ) {
				if ( $startSeparatorFound ) {
					$data .= $bufer;
					if ( $endSeparator && preg_match( $endSeparator, $data ) ) {
						break;
					}
				}
				else {
					if ( preg_match( $startSeparator, $bufer) ) {
						$startSeparatorFound = true;
					}
				}
			}
			else {
				$data .= $bufer;
				if ( $endSeparator && preg_match( $endSeparator, $data ) ) {
					break;
				}
			}
		} while ( !feof( $socket ) );

		// закрываем сокет
		fclose( $socket );

		return $data;
	}


	// подготовка регулярки
	private static function RegExpPrepare( $string ) {
		if ( $string === '' ) {
			return false;
		}

		// вообще magic_quotes_gpc может доставить проблемы, лучше выключать в php.ini
		if ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() ) {
			$string = stripslashes( $string );
		}

		$str[] = '/';
		$replace[] = '\/';

		$str[] = '+';
		$replace[] = '\+';

		$str[] = '>';
		$replace[] = '\>';

		$str[] = '<';
		$replace[] = '\<';

		$str[] = '(';
		$replace[] = '\(';

		$str[] = ')';
		$replace[] = '\)';

		$str[] = '[';
		$replace[] = '\[';

		$str[] = ']';
		$replace[] = '\]';

		/*
		$str[] = ;
		$replace[] = ;
		*/

		$string = str_replace( $str, $replace, $string );
		return '/('.$string.')/si';
	}

	// преобразование строки из одной кодировки в другую
	private static function ConvertEncoding( $string, $toEncoding, $fromEncoding ) {
		$string = mb_convert_encoding( $string, $toEncoding, $fromEncoding );
		return $string;
	}
}
?>