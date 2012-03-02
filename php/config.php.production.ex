<?php
// полный путь к корневой папке баров
define( 'SIG_BASEDIR', $_SERVER[ 'DOCUMENT_ROOT'].'/sig/' );

// подключение к базе данных
define( 'DB_HOST', '' );
define( 'DB_NAME', '' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );

// префикс для таблиц
define( 'DB_TABLE_PREFIX', 'sc2tv_sig_' );

// настройки cron
// кол-во баров для одного запуска
define( 'NUM_SIG', 16 );

// лимит на время работы скрипта
define( 'TIME_LIMIT', 30 );

/**
 * время на сохранение в базе номера следующего бара, с которого начнется генерация
 * бессмысленно делать его меньше времени генерации одного бара, т.к. иначе скрипт все
 * равно не успеет сгенерировать бар
*/
define( 'DB_TIME_LIMIT', 4 );

// сброс ошибок в log true
define( 'LOG_ERRORS', true );
define( 'ERROR_FILE', SIG_BASEDIR . '/error__h3h5w4hsefsfsg.txt' );

error_reporting( E_ALL );
?>