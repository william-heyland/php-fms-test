<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

/**
 * Interface to a database
 */
interface DatabaseInterface {

  /**
   * Set the hostname of the mysql database server
   *
   * @param string $hostname
   *
   * @return $this
   */
  public function setHostname( $hostname );

  /**
   * Set the username of the mysql database server
   *
   * @param string $username
   *
   * @return $this
   */
  public function setUsername( $username );

  /**
   * Set the password of the mysql database server
   *
   * @param string $password
   *
   * @return $this
   */
  public function setPassword( $password );

  /**
   * Set the database_name of the mysql database server
   *
   * @param string $database_name
   *
   * @return $this
   */
  public function setDatabaseName( $database_name );

  /**
   * Connect to database server
   *
   * @return $this
   */
  public function connect();
  
  /*
   * Disconnect from database server
   *
   * @return $this
   */
  public function disconnect();
  
  /**
   * Obtain a database table
   *
   * @param string $table_name
   *
   * @return Table
   */
  public function table( $table_name );

  /**
   * Start Database Transation
   *
   * @return $this
   */
  function startDbTransaction();  

  /**
   * Rollback Database Transation
   *
   * @return $this
   */
  function rollbackDbTransaction(); 

  /**
   * Commit Database Transation
   *
   * @return $this
   */
  function commitDbTransaction();
}

?>