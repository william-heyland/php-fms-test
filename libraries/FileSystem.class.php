<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class DateTime into the FMS namespace */
use DateTime;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

require_once(ROOT_PATH.'interfaces/FileSystemInterface.php');

/**
 * File System Management
 *
 * The FileSystem class implements the FileSystemInterface
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
   * We store the root folder here for convenience since it should only ever be created once.
   * "There Can Only Be One"
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

    try
    {
      /* Fetch the root folder */
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
          ->setName('ROOT')
          ->setPath('/');

        $this->createRootFolder( $this->root_folder );
      }
      else {
        /* Unknown error, throw RuntimeException */
        throw new RuntimeException('Could not instantiate a FileSystem object', RUNTIME_ERROR);
      }
    }

  }

  /**
   * @param FileInterface   $file
   * @param FolderInterface $parent
   *
   * @return FileInterface
   */
  public function createFile(FileInterface $file, FolderInterface $parent)
  {
    /* Use a database transaction to ensure the filename is unique within this folder.*/

    /* Start a new database transaction */
    $this->database->startDbTransaction();

    try
    {
      /* Check if a file with this path already exists */
      if ( $this->getFileByPath( $parent->getPath(), $file->getName() ) )
        throw new Exception( 'File already exists.', FILE_IMPORT_ERROR );

      /* Update the file instance */
      $file
        ->setParentFolder($parent)
        ->setSize( filesize( $file->getImportFilePath() ) )
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

      /* Import the file into the UPLOADS_PATH folder */
      if ( !copy( $file->getImportFilePath(), UPLOADS_PATH.$file_id ) )
        throw new Exception( 'Failed to move file to the UPLOADS_PATH folder.', FILE_IMPORT_ERROR );

    }
    catch ( Exception $e )
    {
      /* Rollback the DB transaction */
      $this->database->rollbackDbTransaction();

      /* We still want to throw the exception up the food chain */
      throw $e;
    }

    /* Commit this database transaction */
    $this->database->commitDbTransaction();

    return $file;
  }

  /**
   * @param FileInterface $file
   *
   * @return FileInterface
   */
  public function updateFile(FileInterface $file)
  {
    /* Use a database transaction to ensure the filename is unique within this folder.*/

    /* Start a new database transaction */
    $this->database->startDbTransaction();

    try
    {
      /* Update the file instance */
      $file
        ->setSize( filesize( $file->getImportFilePath() ) )
        ->setModifiedTime(new DateTime());

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

      /* Import the file into the UPLOADS_PATH folder */
      if ( !copy( $file->getImportFilePath(), UPLOADS_PATH.$file_id ) )
        throw new Exception( 'Failed to move file to the UPLOADS_PATH folder.', FILE_IMPORT_ERROR );
    }
    catch ( Exception $e )
    {
      /* Rollback the DB transaction */
      $this->database->rollbackDbTransaction();

      /* We still want to throw the exception up the food chain */
      throw $e;
    }
    
    /* Commit this database transaction */
    $this->database->commitDbTransaction();

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
    /* Use a database transaction to ensure the filename is unique within this folder. */

    /* Start a new database transaction */
    $this->database->startDbTransaction();

    try
    {
      /* Check if a file with this path already exists */
      if ( $this->getFileByPath( $file->getParentFolder()->getPath(), $newName ) )
        throw new Exception( 'File already exists.', FILE_IMPORT_ERROR );

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

    }
    catch ( Exception $e )
    {
      /* Rollback the DB transaction */
      $this->database->rollbackDbTransaction();

      /* We still want to throw the exception up the food chain */
      throw $e;
    }

    /* Commit this database transaction */
    $this->database->commitDbTransaction();

    return $file;
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
    /* Use a database transaction to ensure the folder is unique. */

    /* Start a new database transaction */
    $this->database->startDbTransaction();

    try
    {
      /* Check if a folder with this path already exists */
      if ( $this->getFolderByPath($parent->getPath().$folder->getName().'/' ) )
        throw new Exception( 'Folder already exists.', FILE_IMPORT_ERROR );

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

    }
    catch ( Exception $e )
    {
      /* Rollback the DB transaction */
      $this->database->rollbackDbTransaction();

      /* We still want to throw the exception up the food chain */
      throw $e;
    }

    /* Commit this database transaction */
    $this->database->commitDbTransaction();

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
    /* Use a database transaction to ensure the folder is unique. */

    /* Start a new database transaction */
    $this->database->startDbTransaction();

    try
    {
      /* Get parent folder */
      $parent = $this->getParentFolder( $folder );

      /* Check if a folder with this name already exists */
      if ( $this->getFolderByPath($parent->getPath().$newName.'/' ) )
        throw new Exception( 'Folder already exists.', FILE_IMPORT_ERROR );

      /* Update the folder instance */
      $folder
        ->setName( $newName );

      /* Update the folder name */
      $this->database->table('folders')->updateFolderName( $folder->getFolderId(), $newName );

    }
    catch ( Exception $e )
    {
      /* Rollback the DB transaction */
      $this->database->rollbackDbTransaction();

      /* We still want to throw the exception up the food chain */
      throw $e;
    }

    /* Commit this database transaction */
    $this->database->commitDbTransaction();

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
  
  /**
   * @param string   $path The path is the full path of the folder including a '/'
   * @param string   $filename
   *
   * @return FileInterface|bool Returns a FileInterface object or false if file does not exist
   */
  protected function getFileByPath( $path, $filename )
  {
    /* Validate $filename */
    $field_spec = array(
      'required' => 'true',
      'type' => 'filename'
    );
    validateInput( $filename, $field_spec );

    /* Validate $path */
    $field_spec = array(
      'required' => 'true',
      'type' => 'path'
    );
    validateInput( $path, $field_spec );

    /* Fetch the file */
    $result = $this->database->table('files')->selectByPath( $path, $filename );

    /* If file does not exist, return false. */
    if ( empty( $result ) )
      return false;

    /* Instantiate a new file */
    $file = new File;
    $file
      ->setFileId($result['file_id'])
      ->setName($result['name'])
      ->setSize($result['size'])
      ->setCreatedTime(new DateTime($result['created_time']));

    if ( !empty( $result['modified_time'] ) )
      $file->setModifiedTime(new DateTime($result['modified_time']));

    return $file;
  }

  /**
   * @param string   $path The path is the full path of the folder including a '/'
   *
   * @return FileInterface|bool Returns a FolderInterface object or false if folder does not exist
   */
  protected function getFolderByPath($path)
  {

    /* Validate $path */
    $field_spec = array(
      'required' => 'true',
      'type' => 'path'
    );
    validateInput( $path, $field_spec );

    /* Fetch the folder */
    $result = $this->database->table('folders')->selectByPath( $path );

    /* If folder does not exist, return false. */
    if ( empty( $result ) )
      return false;

    /* Instantiate Folder */
    $folder = new Folder;
    $folder
      ->setFolderId($result['folder_id'])
      ->setName($result['name'])
      ->setCreatedTime(new DateTime($result['created_time']))
      ->setPath($result['path']);

    return $folder;
  }
}
?>