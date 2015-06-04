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
    $SQL = 'SELECT * FROM folders WHERE parent_folder_id IS NULL ';

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
    $SQL = 'SELECT * FROM folders WHERE folder_id IN ( SELECT parent_folder_id FROM '.$this->table_name." WHERE folder_id = '".$this->db_connection->real_escape_string( $folder_id )."' ) ";

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
   * Select By Path
   *
   * @param string $path
   *
   * @return array
   */
  public function selectByPath( $path )
  {
    /* Construct the SQL query */
    $SQL = "SELECT * FROM folders WHERE path = '".$this->db_connection->real_escape_string( $path )."' ";

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
   * Update Folder Name
   *
   * Updating a folder name affects it's own path, and the path of all it's children. We potentially need to update many database rows
   *
   * @param int $folder_id
   * @param string $name
   *
   * @return bool
   */
  public function updateFolderName( $folder_id, $name )
  {
    /* Construct the SQL query to fetch the current path */
    $SQL_SELECT_PATH = "SELECT path, name FROM folders WHERE folder_id = '".$this->db_connection->real_escape_string( $folder_id )."' ";

    /* Run the query */
    if ( !$result = $this->db_connection->query( $SQL_SELECT_PATH ) )
    {
      throw new Exception('Failed to run database query: '.$SQL_SELECT_PATH, DB_QUERY_ERROR);
    }

    /* Fetch the result as an associative array */
    $row = $result->fetch_assoc();
    
    /* Get the old path */
    $old_path = $row['path'];
    
    /* Set the new path */
    $new_path = preg_replace('/[^\/]*\/$/', $name.'/', $old_path);

    /* Construct the SQL query to update the folder paths */
    $SQL_UPDATE_PATHS = "UPDATE folders SET path = REPLACE(path, '".$old_path."', '".$new_path."' )";

    /* Run the query */
    if ( !$result = $this->db_connection->query( $SQL_UPDATE_PATHS ) )
    {
      throw new Exception('Failed to run database query: '.$SQL_UPDATE_PATHS, DB_QUERY_ERROR);
    }
    
    /* Construct the SQL query to update the folder name */
    $SQL_UPDATE_NAME = "UPDATE folders SET name = '".$this->db_connection->real_escape_string( $name )."' WHERE folder_id = '".$this->db_connection->real_escape_string( $folder_id )."' ";

    /* Run the query */
    if ( !$result = $this->db_connection->query( $SQL_UPDATE_NAME ) )
    {
      throw new Exception('Failed to run database query: '.$SQL_UPDATE_NAME, DB_QUERY_ERROR);
    }

    return true;
  }
}

?>