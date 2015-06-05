<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

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
    $SQL = "SELECT * FROM files JOIN folders ON files.name = ? AND files.parent_folder_id = folders.folder_id AND folders.path = ? ";

    /* Create a prepared statement */
    if ( !$stmt = $this->db_connection->prepare( $SQL ) )
      throw new RuntimeException('Failed to create prepared statement: '.$SQL, DB_QUERY_ERROR);

    /* Bind parameters to prepared statement */
    if ( !$stmt->bind_param('ss', $filename, $path) )
      throw new RuntimeException('Failed to bind parameters to prepared statement: '.$SQL, DB_QUERY_ERROR);

    /* Execute the prepared statement */
    if ( !$stmt->execute() )
      throw new RuntimeException('Failed to execute prepared statement: '.$SQL, DB_QUERY_ERROR);

    /* Get result set */
    if ( !$result = $stmt->get_result() )
      throw new RuntimeException('Failed to retrieve result set: '.$SQL, DB_QUERY_ERROR);

    /* Fetch the result as an associative array */
    $row = $result->fetch_assoc();

    /* Close the statement */
    $stmt->close();

    return $row;
  }
}

?>