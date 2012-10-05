<?php
// полный путь к корневой папке баров
define( 'SIG_BASEDIR', '/sig/' );

// подключение к базе данных
define( 'DB_HOST', '' );
define( 'DB_NAME', '' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );

// префикс для таблиц
define( 'DB_TABLE_PREFIX', 'sc2tv_sig_' );

define( 'SIG_TIMEZONE', 'Europe/Moscow' );
// настройки cron
// кол-во баров для одного запуска
define( 'NUM_SIG', 8 );

// лимит на время работы скрипта
define( 'TIME_LIMIT', 30 );

/**
 * время на сохранение в базе номера следующего бара, с которого начнется генерация
 * бессмысленно делать его меньше времени генерации одного бара, т.к. иначе скрипт все
 * равно не успеет сгенерировать бар
*/
define( 'DB_TIME_LIMIT', 4 );

// время жизни необновляемого бара в секундах, после которого он будет удален
define( 'SIG_TTL', 2592000 );

// сброс ошибок в log true
define( 'LOG_ERRORS', true );
define( 'ERROR_FILE', SIG_BASEDIR . 'php/____error_5hshs5hs5h.txt' );
define( 'DEBUG_FILE', SIG_BASEDIR . 'php/____debug_89t48th4t3rt38h3t.txt' );

error_reporting( E_ALL );
define( 'CURRENT_TIME', time() );
date_default_timezone_set( SIG_TIMEZONE );
?>