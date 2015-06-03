<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class DateTime into the FMS namespace */
use DateTime;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

require_once(ROOT_PATH.'interfaces/FileSystemInterface.php');

/**
 * File System Management
 */
class FileSystem implements FileSystemInterface
{
  /**
   * $database storage engine
   *
   * @var Database $database
   */
  protected $database;

  /**
   * The root folder
   *
   * We store the root folder here for convenience
   *
   * @var Folder $root_folder
   */
  protected $root_folder;

  /**
   * Initialise FileSystem
   */
  function __construct()
  {
    /* Connect to the database storage engine */
    $this->database = new Database();

    /* Connect to the mysql database */
    $this->database
      ->setHostname(DB_HOST)
      ->setUsername(DB_USERNAME)
      ->setPassword(DB_PASSWORD)
      ->setDatabaseName(DB_NAME)
      ->connect();

    /* Fetch the root folder */
    try
    {
       $this->root_folder = $this->getRootFolder();
    }
    catch (Exception $e)
    {
      /* Does the root folder exist? */
      if ( $e->getCode() == MISSING_ROOT_FOLDER )
      {
        /* Create the root folder */
        $this->root_folder = new Folder();
        $this->root_folder
          ->setName('root folder')
          ->setPath('/');

        $this->createRootFolder( $this->root_folder );
      }
    }
  }

  public function setDatabase( Database $database )
  {
    $this->database = $database;
  }

  /**
   * @param FileInterface   $file
   * @param FolderInterface $parent
   *
   * @return FileInterface
   */
  public function createFile(FileInterface $file, FolderInterface $parent)
  {
    /* TODO: Ensure the filename is unique within this folder. The relational database already enforces uniqueness, but it's better practice to do it here. */


    try
    {
      $this->database->startDbTransaction();

      /* Update the file instance */
      $file
        ->setParentFolder($parent)
        ->setCreatedTime(new DateTime());

      /* Prepare data for database input */
      $data = array(
        'name' => $file->getName(),
        'size' => $file->getSize(),
        'created_time' => $file->getCreatedTime()->format('Y-m-d H:i:s'),
        'parent_folder_id' => $parent->getFolderId()
      );

      /* Insert the file into the database */
      $file_id = $this->database->table('files')->insert( $data );

      /* Set the file id */
      $file->setFileId( $file_id );

      /* Import the file into the FMS_UPLOADS folder */
      if ( !copy( $this->import_file_path, FMS_UPLOADS.$file_id ) )
        throw new Exception( 'Failed to move file to the FMS_UPLOADS folder.', FILE_IMPORT_ERROR );
      
      $this->database->commitDbTransaction();
    }
    catch ( Exception $e )
    {
      $this->database->rollbackDbTransaction();
    }
    


    return $file;
  }

  /**
   * @param FileInterface $file
   *
   * @return FileInterface
   */
  public function updateFile(FileInterface $file)
  {
    /* Update the file instance */
    $file->setModifiedTime(new DateTime());

    /* Prepare data for database input */
    $data = array(
      'size' => $file->getSize(),
      'parent_folder_id' => $file->getParentFolder()->getFolderId(),
      'modified_time' => $file->getModifiedTime()->format('Y-m-d H:i:s')
    );
    /* Prepare where clause data */
    $where = array(
       'file_id' => $file->getFileId()
    );

    /* Update the file in the database */
    $this->database->table('files')->update( $data, $where );

    return $file;
  }

  /**
   * @param FileInterface $file
   * @param               $newName
   *
   * @return FileInterface
   */
  public function renameFile(FileInterface $file, $newName)
  {
    /* TODO: Ensure the filename is unique within this folder. The relational database already enforces uniqueness, but it's better practice to do it here. */
    
    /* Update the file instance */
    $file
      ->setName( $newName )
      ->setModifiedTime(new DateTime());

    /* Prepare data for database input */
    $data = array(
      'name' => $file->getName(),
      'modified_time' => $file->getModifiedTime()->format('Y-m-d H:i:s')
    );

    /* Prepare where clause data */
    $where = array(
      'file_id' => $file->getFileId()
    );

    /* Update the file in the database */
    $this->database->table('files')->update( $data, $where );

    return true;
  }

  /**
   * @param FileInterface $file
   *
   * @return bool
   */
  public function deleteFile(FileInterface $file)
  {
    /* Prepare where clause data */
    $where = array(
      'file_id' => $file->getFileId()
    );

    /* Delete the file from the database */
    $this->database->table('files')->delete( $where );

    return true;
  }

  /**
   * @param FolderInterface $folder
   *
   * @return FolderInterface
   */
  public function createRootFolder(FolderInterface $folder)
  {
    /* TODO: Limit the system to a single Root folder */

    /* Update the folder instance */
    $folder
      ->setCreatedTime(new DateTime());

    /* Prepare data for database input */
    $data = array(
      'name' => $folder->getName(),
      'path' => '/',
      'created_time' => $folder->getCreatedTime()->format('Y-m-d H:i:s')
    );

    /* Insert the folder into the database */
    $folder_id = $this->database->table('folders')->insert( $data );

    /* Set Folder Id */
    $folder->setFolderId( $folder_id );

    return $folder;
  }

  /**
   * @param FolderInterface $folder
   * @param FolderInterface $parent
   *
   * @return FolderInterface
   */
  public function createFolder( FolderInterface $folder, FolderInterface $parent )
  {
    /* TODO: Ensure the folder name is unique within this folder. The relational database already enforces uniqueness, but it's better practice to do it here. */

    /* Update the folder instance */
    $folder
      ->setPath($parent->getPath().$folder->getName().'/')
      ->setCreatedTime(new DateTime());

    /* Prepare data for database input */
    $data = array(
      'name' => $folder->getName(),
      'path' => $folder->getPath(),
      'parent_folder_id' => $parent->getFolderId(),
      'created_time' => $folder->getCreatedTime()->format('Y-m-d H:i:s')
    );

    /* Insert the folder into the database */
    $folder_id = $this->database->table('folders')->insert( $data );

    /* Set the folder id */
    $folder->setFolderId( $folder_id );

    return $folder;
  }

  /**
   * @param FolderInterface $folder
   *
   * @return bool
   */
  public function deleteFolder(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'folder_id' => $folder->getFolderId()
    );

    /* Delete the folder from the database */
    $this->database->table('folders')->delete( $where );

    return true;
  }

  /**
   * @param FolderInterface $folder
   * @param                 $newName
   *
   * @return FolderInterface
   */
  public function renameFolder(FolderInterface $folder, $newName)
  {
    /* TODO: Ensure the folder name is unique within this folder. The relational database already enforces uniqueness, but it's better practice to do it here. */

    /* Update the folder instance */
    $folder
      ->setName( $newName );

    /* Prepare data for database input */
    $data = array(
      'name' => $folder->getName()
    );

    /* Prepare where clause data */
    $where = array(
      'folder_id' => $folder->getFolderId()
    );

    /* Update the folder name and modified time in the database */
    $this->database->table('folders')->update( $data, $where );

    return true;
  }

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getFolderCount(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'parent_folder_id' => $folder->getFolderId()
    );

    /* Update the folder name and modified time in the database */
    $results = $this->database->table('folders')->select( $where );

    return count($results);
  }

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getFileCount(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'parent_folder_id' => $folder->getFolderId()
    );

    /* Update the folder name and modified time in the database */
    $results = $this->database->table('files')->select( $where );

    return count($results);
  }

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getDirectorySize(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'parent_folder_id' => $folder->getFolderId()
    );

    /* Update the folder name and modified time in the database */
    $results = $this->database->table('files')->select( $where );

    /* Calculate total size of all files within $folder */
    $size = 0;
    foreach( $results as $result )
    {
      $size += $result['size'];
    }

    return $size;
  }

  /**
   * @param FolderInterface $folder
   *
   * @return FolderInterface[]
   */
  public function getFolders(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'parent_folder_id' => $folder->getFolderId()
    );

    /* Fetch the folders */
    $results = $this->database->table('folders')->select( $where );

    /* Instantiate Folders */
    $folders = array();
    foreach( $results as $result )
    {
      $folder = new Folder;
      $folder
        ->setFolderId($result['folder_id'])
        ->setName($result['name'])
        ->setCreatedTime(new DateTime($result['created_time']))
        ->setPath($result['path']);

      $folders[] = $folder;
    }

    return $folders;
  }
  
  /**
   * Get The Root Folder
   *
   * "There Can Only Be One"
   *
   * @return FolderInterface
   */
  public function getRootFolder()
  {
    /* Return the $this->root_folder if we have already fetched it */
    if ( !empty( $this->root_folder ) )
      return $this->root_folder;

    /* Fetch the root folder */
    $result = $this->database->table('folders')->selectRootFolder();

    if ( empty( $result ) )
    {
      throw new Exception('Missing root folder', MISSING_ROOT_FOLDER);
    }
 
    /* Instantiate Folder */
    $folder = new Folder;
    $folder
      ->setFolderId($result['folder_id'])
      ->setName($result['name'])
      ->setCreatedTime(new DateTime($result['created_time']))
      ->setPath($result['path']);

    return $folder;
  }

  /**
   * Get Parent Folder
   *
   * @param FolderInterface $folder
   *
   * @return FolderInterface
   */
  public function getParentFolder(FolderInterface $folder)
  {
    /* Fetch the parent folder */
    $result = $this->database->table('folders')->selectParentFolder( $folder->getFolderId() );

    if ( empty( $result ) )
    {
      throw new Exception('Missing root folder.', INVALID_INPUT);
    }
 
    /* Instantiate Folder */
    $folder = new Folder;
    $folder
      ->setFolderId($result['folder_id'])
      ->setName($result['name'])
      ->setCreatedTime(new DateTime($result['created_time']))
      ->setPath($result['path']);

    return $folder;
  }

  /**
   * @param FolderInterface $folder
   *
   * @return FileInterface[]
   */
  public function getFiles(FolderInterface $folder)
  {
    /* Prepare where clause data */
    $where = array(
      'parent_folder_id' => $folder->getFolderId()
    );

    /* Fetch the files */
    $results = $this->database->table('files')->select( $where );

    /* Instantiate Files */
    $files = array();
    foreach( $results as $result )
    {
      $file = new File;
      $file
        ->setFileId($result['file_id'])
        ->setName($result['name'])
        ->setSize($result['size'])
        ->setCreatedTime(new DateTime($result['created_time']))
        ->setParentFolder($folder);
        
      if ( !empty( $result['modified_time'] ) )
        $file->setModifiedTime(new DateTime($result['modified_time']));
        
      $files[] = $file;
    }

    return $files;
  }
}
