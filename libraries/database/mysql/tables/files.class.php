<?php

if (!defined('SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Table.class.php');

/**
 * The files class provides a clean interface to the "files" database table
 *
 */
class files extends Table {
  protected $table_name = 'files';
  
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
