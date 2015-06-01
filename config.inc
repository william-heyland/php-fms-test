<?php

if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);

/**
 * ROOT_PATH
 *
 * The full path to the project root folder including the trailing slash
 */
define( 'ROOT_PATH', '/home/jdi/php-fms-test/' );

/**
 * DB_USER
 *
 * Database connection credentials for the database username
 */
define( 'DB_USERNAME', 'jdi' );

/**
 * DB_PASSWORD
 *
 * Database connection credentials for the database password
 */
define( 'DB_PASSWORD', 'jdi' );

/**
 * DB_NAME
 *
 * Database connection credentials for the database name
 */
define( 'DB_NAME', 'jdi' );

/**
 * DB_HOST
 *
 * Database connection credentials for the databse hostname
 */
define( 'DB_HOST', 'localhost' );

/**
 * DB_ENGINE
 *
 * Database engine to use. The database libraries are located in a subfolder of ROOT_PATH.'/libraries/database/'.DB_ENGINE.
 * The Database class support multiple database back-ends.
 */
define( 'DB_ENGINE', 'mysql' );

/**
 * Common error codes used in my exception handling
 */
define( 'INVALID_INPUT', 1 );
define( 'MISSING_INPUT', 2 );
define( 'DB_CONNECT_ERROR', 100 );
define( 'DB_UNKNOWN_TABLE_ERROR', 101 );
define( 'DB_QUERY_ERROR', 102 );
define( 'SECURITY_ALERT', 200 );

/**
 * Perl regular expression input validation
 */
define( 'PERL_REGEX_FILENAME_VALIDATION', '/^[0-9a-zA-Z\-_ ]{1,256}$/i' );
define( 'PERL_REGEX_PATH_VALIDATION', '/^\/([0-9a-zA-Z\-_ \/]{1,8190}\/)?$/i' );
define( 'PERL_REGEX_DATETIME_VALIDATION', '/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$/i' );
?>