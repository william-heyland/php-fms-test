<?php

if (!defined('SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Table.class.php');

/**
 * The folders class provides a clean interface to the "folders" database table
 *
 */
class folders extends Table {
  protected $table_name = 'folders';
  
  protected $table_columns = array(
    'folder_id',
    'name',
    'created_time',
    'path',
    'parent_folder_id'
  );
}

?> 
