<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'libraries/database/'.DB_ENGINE.'/Table.class.php');

/**
 * The folders class provides a clean interface to the "folders" database table
 *
 */
class folders extends Table {

  /**
   * Table name
   *
   * @var string The database table name
   */
  protected $table_name = 'folders';

  /**
   * Table columns
   *
   * @var array The database table columns
   */
  protected $table_columns = array(
    'folder_id',
    'name',
    'created_time',
    'path',
    'parent_folder_id'
  );

  /**
   * Select Root Folder
   *
   * @return array
   */
  public function selectRootFolder()
  {
    /* Construct the SQL query */
    $SQL = 'SELECT * FROM '.$this->table_name.' WHERE parent_folder_id IS NULL ';

    /* Run the query */
    if ( !$result = $this->db_connection->query( $SQL ) )
    {
      throw new Exception('Failed to run database query: '.$SQL, DB_QUERY_ERROR);
    }
    
    /* Fetch the result as an associative array */
    $row = $result->fetch_assoc();

    return $row;
  }

  /**
   * Select Parent Folder
   *
   * @param int $folder_id
   *
   * @return array
   */
  public function selectParentFolder( $folder_id )
  {
    /* Construct the SQL query */
    $SQL = 'SELECT * FROM '.$this->table_name.' WHERE folder_id IN ( SELECT parent_folder_id FROM '.$this->table_name." WHERE folder_id = '".$this->db_connection->real_escape_string( $folder_id )."' ) ";

    /* Run the query */
    if ( !$result = $this->db_connection->query( $SQL ) )
    {
      throw new Exception('Failed to run database query: '.$SQL, DB_QUERY_ERROR);
    }

    /* Fetch the result as an associative array */
    $row = $result->fetch_assoc();

    return $row;
  }
}

?> 
