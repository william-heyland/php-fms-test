<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

/**
 * ROOT_PATH
 *
 * The full path to the project root folder including the trailing slash
 */
define( 'FMS\ROOT_PATH', '/home/jdi/php-fms-test/' );

/**
 * DB_USER
 *
 * Database connection credentials for the database username
 */
define( 'FMS\DB_USERNAME', 'jdi' );

/**
 * DB_PASSWORD
 *
 * Database connection credentials for the database password
 */
define( 'FMS\DB_PASSWORD', 'jdi' );

/**
 * DB_NAME
 *
 * Database connection credentials for the database name
 */
define( 'FMS\DB_NAME', 'jdi' );

/**
 * DB_HOST
 *
 * Database connection credentials for the databse hostname
 */
define( 'FMS\DB_HOST', 'localhost' );

/**
 * DB_ENGINE
 *
 * Database engine to use. The database libraries are located in a subfolder of ROOT_PATH.'/libraries/database/'.DB_ENGINE.
 * The Database class support multiple database back-ends.
 */
define( 'FMS\DB_ENGINE', 'mysql' );

/**
 * UPLOADS_PATH
 *
 * The location on disk of files uploaded to the FMS.
 * It's generally a bad idea to store and serve files directly from a database so the actual FMS files will be stored in the UPLOADS_PATH folder
 */
define( 'FMS\UPLOADS_PATH', ROOT_PATH.'fms_uploads/' );

/**
 * Common error codes used in my exception handling
 */
define( 'FMS\INVALID_INPUT', 1 );
define( 'FMS\MISSING_INPUT', 2 );
define( 'FMS\MISSING_ROOT_FOLDER', 3 );
define( 'FMS\DB_CONNECT_ERROR', 100 );
define( 'FMS\DB_UNKNOWN_TABLE_ERROR', 101 );
define( 'FMS\DB_QUERY_ERROR', 102 );
define( 'FMS\SECURITY_ALERT', 200 );
define( 'FMS\FILE_IMPORT_ERROR', 300 );
define( 'FMS\RUNTIME_ERROR', 400 );

/**
 * Perl regular expression input validation
 */
define( 'FMS\PERL_REGEX_FILENAME_VALIDATION', '/^[0-9a-zA-Z\-_.]{1,256}$/i' );
define( 'FMS\PERL_REGEX_FOLDERNAME_VALIDATION', '/^[0-9a-zA-Z\-_]{1,256}$/i' );
define( 'FMS\PERL_REGEX_PATH_VALIDATION', '/^\/([0-9a-zA-Z\-_\/]{1,8190}\/)?$/i' );
define( 'FMS\PERL_REGEX_DATETIME_VALIDATION', '/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$/i' );
?>