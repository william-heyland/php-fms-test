<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Table.class.php');

/**
 * The files class provides a clean interface to the "files" database table
 *
 */
class files extends Table {

  /**
   * Table name
   *
   * @var string The database table name
   */
  protected $table_name = 'files';

  /**
   * Table columns
   *
   * @var array The database table columns
   */
  protected $table_columns = array(
    'file_id',
    'name',
    'size',
    'created_time',
    'modified_time',
    'path',
    'parent_folder_id'
  );
}

?> 
