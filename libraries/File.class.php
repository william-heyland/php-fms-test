<?php

if (!defined('SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'interfaces/FileInterface.php');

class File implements FileInterface
{
  /**
   * @var int File identifier
   */
  protected $file_id;

  /**
   * @var string File name
   */
  protected $name;

  /**
   * @var int File size
   */
  protected $size;

  /**
   * @var dateTime File created time
   */
  protected $created_time;

  /**
   * @var dateTime File modified time
   */
  protected $modified_time;

  /**
   * @var FolderInterface File parent folder
   */
  protected $parent_folder;
  
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
  public function getFileId()
  {
    return $this->file_id;
  }

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setFileId($file_id)
  {
    /* Validate file_id */
    $field_spec = array(
      'required' => 'true',
      'type' => 'integer'
    );

    validateInput( $file_id, $field_spec );

    $this->file_id = $file_id;
    
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
    /* Validate filename */
    $field_spec = array(
      'required' => 'true',
      'type' => 'filename'
    );

    validateInput( $name, $field_spec );

    $this->name = $name;

    return $this;
  }

  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }

  /**
   * @param int $size
   *
   * @return $this
   */
  public function setSize($size)
  {
    /* Validate size */
    $field_spec = array(
      'required' => 'true',
      'type' => 'integer'
    );

    validateInput( $size, $field_spec );

    $this->size = $size;

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
   * @return DateTime
   */
  public function getModifiedTime()
  {
    return $this->modified_time;
  }

  /**
   * @param DateTime $modified
   *
   * @return $this
   */
  public function setModifiedTime(DateTime $modified)
  {
    $this->modified_time = $modified;

    return $this;
  }

  /**
   * @return FolderInterface
   */
  public function getParentFolder()
  {
    return $this->parent_folder;
  }

  /**
   * @param FolderInterface $parent
   *
   * @return $this
   */
  public function setParentFolder(FolderInterface $parent)
  {
    $this->parent_folder = $parent;

    return $this;
  }

  /**
   * @return string
   */
  public function getPath()
  {
    return $this->parent_folder->getPath().$this->parent_folder->getName().'/';
  }
}
