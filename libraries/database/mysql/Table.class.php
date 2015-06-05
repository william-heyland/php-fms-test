<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

require_once(ROOT_PATH.'interfaces/DatabaseTableInterface.php');

/**
 * The Table class provides a clean api to each database table
 *
 */
class Table implements DatabaseTableInterface {

  /**
   * Database connection
   */
  protected $db_connection;

  /**
   * Table name
   *
   * @var string The database table name
   */
  protected $table_name = '';
  
  /**
   * Table columns
   *
   * @var array The database table columns
   */
  protected $table_columns = array();

  /**
   * Initialise table object
   */
  public function setDatabaseConnection( $database_connection )
  {
    $this->db_connection = $database_connection;
    
    return true;
  }

  /**
   * Insert
   */
  public function insert( array $data )
  {
    /* Construct the SQL query */
    $SQL = 'INSERT INTO '.$this->table_name.' ';
    $SQL_COLUMNS = '';
    $SQL_VALUES = '';
    $COMA = '';

    /* Build the query based on the data provided */
    foreach ( $data as $key => $value )
    {
      if ( !in_array( $key, $this->table_columns ) )
        throw new Exception('Unknown database column: '.$key, INVALID_INPUT);


      $SQL_COLUMNS .= $COMA.' `'.$key.'` ';
      $SQL_VALUES .= $COMA." '".$this->db_connection->real_escape_string($value)."' ";
      $COMA = ', ';
    }

    /* Put it all together */
    $SQL .= ' ('.$SQL_COLUMNS.') VALUES ('.$SQL_VALUES.')';

    /* Run the INSERT query */
    if ( !$this->db_connection->query( $SQL ) )
      throw new RuntimeException('Failed to run database query: '.$SQL, DB_QUERY_ERROR);


    /* Get the row id */
    if ( !$id = $this->db_connection->insert_id )
      throw new RuntimeException('Failed to fetch last insert id', DB_QUERY_ERROR);


    /* Return the row id */
    return $id;
  }

  /**
   * Update
   */
  public function update( array $data, array $where = array() )
  {
    /* Construct the SQL query */
    $SQL = 'UPDATE '.$this->table_name.' ';

    /* Build the SET clause based on the data provided */
    $SQL_SET_CLAUSE = '';
    $COMA = '';
    foreach ( $data as $key => $value )
    {
      if ( !in_array( $key, $this->table_columns ) )
        throw new Exception('Unknown database column: '.$key, INVALID_INPUT);

      $SQL_SET_CLAUSE .= $COMA.' `'.$key."` = '".$this->db_connection->real_escape_string($value)."' ";
      $COMA = ', ';
    }
    
    /* Build the WHERE clause based on the data provided */
    $SQL_WHERE_CLAUSE = '';
    $AND = '';
    foreach ( $where as $key => $value )
    {
      if ( !in_array( $key, $this->table_columns ) )
        throw new Exception('Unknown database column: '.$key, INVALID_INPUT);

      $SQL_WHERE_CLAUSE .= $AND.' `'.$key."` = '".$this->db_connection->real_escape_string($value)."' ";
      $AND = ' AND ';
    }
    
    /* Don't allow an UPDATE without a where clause */
    if ( empty( $SQL_WHERE_CLAUSE ) )
      throw new RuntimeException('Not permitted to UPDATE without a WHERE clause in the "update()" method of the Table class', INVALID_INPUT);

    /* Put it all together */
    $SQL .= ' SET '.$SQL_SET_CLAUSE.' WHERE '.$SQL_WHERE_CLAUSE.' ';

    /* Run the UPDATE query */
    if ( !$this->db_connection->query( $SQL ) )
      throw new RuntimeException('Failed to run database query: '.$SQL, DB_QUERY_ERROR);


    return true;
  }

  /**
   * Select
   */
  public function select( array $where = array() )
  {
    /* Construct the SQL query */
    $SQL = 'SELECT * FROM '.$this->table_name.' ';

    /* Build the WHERE clause based on the data provided */
    $SQL_WHERE_CLAUSE = '';
    $AND = '';
    foreach ( $where as $key => $value )
    {
      if ( !in_array( $key, $this->table_columns ) )
        throw new Exception('Unknown database column: '.$key, INVALID_INPUT);


      $SQL_WHERE_CLAUSE .= $AND.' `'.$key."` = '".$this->db_connection->real_escape_string($value)."' ";
      $AND = ' AND ';
    }

    /* Put it all together */
    if ( !empty( $SQL_WHERE_CLAUSE ) )
      $SQL .= ' WHERE '.$SQL_WHERE_CLAUSE;

    /* Run the SELECT query */
    if ( !$result = $this->db_connection->query( $SQL ) )
      throw new RuntimeException('Failed to run database query: '.$SQL, DB_QUERY_ERROR);
    
    /* Put results into a single array */
    $rows = array();
    while( $row = $result->fetch_assoc() )
      $rows[] = $row;

    return $rows;
  }

  /**
   * Delete
   */
  public function delete( array $where = array() )
  {
    /* Construct the SQL query */
    $SQL = 'DELETE FROM '.$this->table_name.' ';

    /* Build the WHERE clause based on the data provided */
    $SQL_WHERE_CLAUSE = '';
    $AND = '';
    foreach ( $where as $key => $value )
    {
      if ( !in_array( $key, $this->table_columns ) )
        throw new Exception('Unknown database column: '.$key, INVALID_INPUT);

      $SQL_WHERE_CLAUSE .= $AND.' `'.$key."` = '".$this->db_connection->real_escape_string($value)."' ";
      $AND = ' AND ';
    }

    /* Don't allow a DELETE without a where clause */
    if ( empty( $SQL_WHERE_CLAUSE ) )
      throw new RuntimeException('Not permitted to DELETE without a WHERE clause in the "delete()" method of the Table class', INVALID_INPUT);

    /* Put it all together */
    $SQL .= ' WHERE '.$SQL_WHERE_CLAUSE;

    /* Run the DELETE query */
    if ( !$this->db_connection->query( $SQL ) )
      throw new RuntimeException('Failed to run database query: '.$SQL, DB_QUERY_ERROR);

    return true;
  }

  /**
   * Empty table contents
   */
  public function emptyTable()
  {
    /* Construct the SQL query */
    $SQL = 'DELETE FROM '.$this->table_name.' ';

    /* Run the DELETE query */
    if ( !$this->db_connection->query( $SQL ) )
      throw new RuntimeException('Failed to run database query: '.$SQL, DB_QUERY_ERROR);

    return true;
  }
}

?>