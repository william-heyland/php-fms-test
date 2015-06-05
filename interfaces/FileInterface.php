<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class DateTime */
use DateTime;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

interface FileInterface
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
   * @return int
   */
  public function getSize();
  
  /**
   * @param int $size
   *
   * @return $this
   */
  public function setSize($size);

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
   * @return DateTime
   */
  public function getModifiedTime();

  /**
   * @param DateTime $modified
   *
   * @return $this
   */
  public function setModifiedTime(DateTime $modified);

  /**
   * @return FolderInterface
   */
  public function getParentFolder();

  /**
   * @param FolderInterface $parent
   *
   * @return $this
   */
  public function setParentFolder(FolderInterface $parent);

  /**
   * @return string
   */
  public function getPath();
}
?>