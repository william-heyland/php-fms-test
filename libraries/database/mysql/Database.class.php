<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'interfaces/DatabaseInterface.php');

/**
 * The Database class provides a clean api to my database storage solution
 *
 */
class Database implements DatabaseInterface {

  /**
   * The hostname of the mysql database server
   *
   * @var string
   */
  protected $hostname;

  /**
   * The username of the mysql database server
   */
  protected $username;

  /**
   * The password of the mysql database server
   *
   * @var string
   */
  protected $password;

  /**
   * The name of the mysql database
   *
   * @var string
   */
  protected $database_name;

  /**
   * Tables
   *
   * Contains a list of current database table objects.
   *
   * @var array
   */
  protected $tables = array();

  /**
   * The database connection
   */
  protected $db_connection;

  /**
   * Set the hostname of the mysql database server
   *
   * @param string $hostname
   */
  public function setHostname( $hostname )
  {
    $this->hostname = $hostname;
    
    return $this;
  }

  /**
   * Set the username of the mysql database server
   *
   * @param string $username
   */
  public function setUsername( $username )
  {
    $this->username = $username;
    
    return $this;
  }

  /**
   * Set the password of the mysql database server
   *
   * @param string $password
   */
  public function setPassword( $password )
  {
    $this->password = $password;
    
    return $this;
  }

  /**
   * Set the database_name of the mysql database server
   *
   * @param string $database_name
   */
  public function setDatabaseName( $database_name )
  {
    $this->database_name = $database_name;
    
    return $this;
  }

  /**
   * Initialise database connection
   */
  public function connect()
  {
    /* Connect to mysql database */
    $this->db_connection = mysqli_connect( $this->hostname, $this->username, $this->password, $this->database_name );
    
    /* Check for database connection errors */
    if (mysqli_connect_error())
    {
      throw new Exception('Failed to connect to database: '.mysqli_connect_error().' ('.mysqli_connect_errno().')', DB_CONNECT_ERROR ); 
    }
    
    return $this;
  }

  /**
   * Close database connection
   */
  public function disconnect()
  {
    /* Disconnect from mysql database */
    $this->db_connection->close();

    return $this;
  }

  /**
   * Obtain a database table object
   *
   * @param string $table_name
   *
   * @return Table
   */
  public function table( $table_name )
  {
    /* If we already have an instance of this table available, return it now */
    if ( isset( $this->tables[$table_name] ) )
    {
      return $this->tables[$table_name];
    }

    /* Determine the path to the current instance */
    $path = ROOT_PATH.'libraries/database/'.DB_ENGINE.'/tables/'.$table_name.'.class.php';

    /* Check the table class definition exists */
    if ( !file_exists( $path ) )
    {
      throw new Exception('Unknown database table', DB_UNKNOWN_TABLE_ERROR);
    }

    /* Include the table class definition */
    require_once($path);

    /* Instantiate the table object */
    $class_name = '\\FMS\\'.$table_name;
    $this->tables[$table_name] = new $class_name();
    $this->tables[$table_name]->setDatabaseConnection( $this->db_connection );

    /* Return the table object */
    return $this->tables[$table_name];
  }

  /**
   * Start Database Transation
   */
  function startDbTransaction() {
    $SQL = " BEGIN ";
    if ( $this->db_connection->query($SQL) === false ) {
      throw new Exception('Failed to start database transaction', DB_QUERY_ERROR);
    }
    return $this;
  }

  /**
   * Rollback Database Transaction
   */
  function rollbackDbTransaction() {
    $SQL = " ROLLBACK ";
    if ( $this->db_connection->query($SQL) === false ) {
      throw new Exception('Failed to rollback database transaction', DB_QUERY_ERROR);
    }
    return $this;
  }

  /**
   * Commit Database Transaction
   */
  function commitDbTransaction() {
    $SQL = " COMMIT ";
    if ( $this->db_connection->query($SQL) === false ) {
      throw new Exception('Failed to commit database transaction', DB_QUERY_ERROR);
    }
    return $this;
  }

}

?>