<?php
// полный путь к корневой папке баров
define( 'SIG_BASEDIR', $_SERVER[ 'DOCUMENT_ROOT'].'/sig/' );

// подключение к базе данных
define( 'DB_HOST', 'localhost' );
define( 'DB_NAME', 'sc2tv_pressflow' );
define( 'DB_USER', 'sc2tv_pressflow' );
define( 'DB_PASSWORD', 'GUvnJamxqRD' );

// префикс для таблиц
define( 'DB_TABLE_PREFIX', 'sc2tv_sig_' );

// сброс ошибок в log true
define( 'LOG_ERRORS', false );
?>