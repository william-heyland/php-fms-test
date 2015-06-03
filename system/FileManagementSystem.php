<?php

/**
 * File Management System
 *
 * Include this file in your PHP project to use this wonderful file management system.
 */

/* Give the project it's own namespace. */
namespace FMS;

/**
 * SECURED
 *
 * The purpose of the SECURED constant is to prevent direct calls to other php scripts in this project.
 * The following condition should be present at the top of every file in this project:
 * if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);
 */
define( 'FMS\SECURED', true );

/* Include the project configuration */
require_once('/home/jdi/php-fms-test/config/config.inc');

/* Include the desired database storage engine */
require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Database.class.php');

/* Include helper functions */
require_once(ROOT_PATH.'libraries/Helpers.php');

/* Include File and Folder class definitions */
require_once(ROOT_PATH.'libraries/File.class.php');
require_once(ROOT_PATH.'libraries/Folder.class.php');

/* Include and FileSystem class definition */
require_once(ROOT_PATH.'libraries/FileSystem.class.php');

?>