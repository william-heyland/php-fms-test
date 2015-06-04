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
  
  /**
   * Select By Path
   *
   * @param string $path
   * @param string $filename
   *
   * @return array
   */
  public function selectByPath( $path, $filename )
  {
    /* Construct the SQL query. */
    $SQL = "SELECT * FROM files JOIN folders ON files.name = '".$this->db_connection->real_escape_string( $filename )."' AND files.parent_folder_id = folders.folder_id AND folders.path = '".$this->db_connection->real_escape_string( $path )."' ";

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