<?php
/**
 * All interactions between the user and the file management system will be routed via this index.php file.
 */

/**
 * SECURED
 *
 * The purpose of the SECURED constant is to prevent direct calls to other php scripts in this project.
 * All interactions between the user and the file management system will be routed via this index.php file.
 * The following condition should be present at the top of every file in this project:
 * if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);
 */
define( 'SECURED', true );

/* Display errors in output and include ALL errors */
ini_set('display_errors', '1');
error_reporting(E_ALL);

/* Include the project configuration */
require_once('/home/jdi/php-fms-test/config.inc');

/* Include and instantiate the Database object */
require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Database.class.php');
$database = new Database();

/* Connect to the mysql database */
$database
  ->setHostname(DB_HOST)
  ->setUsername(DB_USERNAME)
  ->setPassword(DB_PASSWORD)
  ->setDatabaseName(DB_NAME)
  ->connect();

/* Include and instantiate the FileSystem object */
require_once(ROOT_PATH.'libraries/FileSystem.class.php');
$filesystem = new FileSystem();
$filesystem->setDatabase( $database );

/* Include File and Folder class definitions */
require_once(ROOT_PATH.'libraries/File.class.php');
require_once(ROOT_PATH.'libraries/Folder.class.php');

/* Include helper functions */
require_once(ROOT_PATH.'libraries/Helpers.php');

?>