<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class DateTime */
use DateTime;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'interfaces/FolderInterface.php');

/**
 * Class Folder
 *
 * The Folder class implements the FolderInterface
 */
class Folder implements FolderInterface
{
  /**
   * @var int Folder identifier
   */
  protected $folder_id;

  /**
   * @var string Folder name
   */
  protected $name;

  /**
   * @var dateTime Folder created time
   */
  protected $created_time;

  /**
   * @var string Folder path
   */
  protected $path;

  /**
   * Set default property values
   */
  function __construct()
  {
    /* Set default created time */
    $this->created_time = new DateTime();
  }

  /**
   * @return int
   */
  public function getFolderId()
  {
    return $this->folder_id;
  }

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setFolderId( $folder_id )
  {
    /* Validate folder_id */
    $field_spec = array(
      'required' => 'true',
      'type' => 'integer'
    );

    validateInput( $folder_id, $field_spec );

    $this->folder_id = $folder_id;

    return $this;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setName($name)
  {
    /* Validate foldername */
    $field_spec = array(
      'required' => 'true',
      'type' => 'foldername'
    );

    validateInput( $name, $field_spec );

    $this->name = $name;

    return $this;
  }

  /**
   * @return DateTime
   */
  public function getCreatedTime()
  {
    return $this->created_time;
  }

  /**
   * @param DateTime $created
   *
   * @return $this
   */
  public function setCreatedTime(DateTime $created)
  {
    $this->created_time = $created;

    return $this;
  }

  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * @param string $path
   *
   * @return $this
   */
  public function setPath($path)
  {
    /* Validate path */
    $field_spec = array(
      'required' => 'true',
      'type' => 'path'
    );

    validateInput( $path, $field_spec );

    $this->path = $path;

    return $this;
  }
}
?>