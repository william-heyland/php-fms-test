<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

/**
 * Inteface to a database table
 *
 * Each table class is free to define more advanced queries but it must at least support this common subset. 
 */
interface DatabaseTableInterface {
  /**
   * Insert
   *
   * @return int
   */
  public function insert( array $data );

  /**
   * Update
   *
   * @return bool
   */
  public function update( array $data, array $where = array() );

  /**
   * Select
   *
   * @return array
   */
  public function select( array $where = array() );

  /**
   * Delete
   *
   * @return bool
   */
  public function delete( array $where = array() );
}

?>