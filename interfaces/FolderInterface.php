<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class DateTime into our namespace */
use DateTime;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

interface FolderInterface
{
  /**
   * @return string
   */
  public function getName();
  
  /**
   * @param string $name
   *
   * @return $this
   */
  public function setName($name);

  /**
   * @return DateTime
   */
  public function getCreatedTime();
  
  /**
   * @param DateTime $created
   *
   * @return $this
   */
  public function setCreatedTime(DateTime $created);

  /**
   * @return string
   */
  public function getPath();
  
  /**
   * @param string $path
   *
   * @return $this
   */
  public function setPath($path);
}
?>