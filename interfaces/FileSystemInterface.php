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

/**
 * File System Management
 */
interface FileSystemInterface
{
  /**
   * @param FileInterface   $file
   * @param FolderInterface $parent
   *
   * @return FileInterface
   */
  public function createFile(FileInterface $file, FolderInterface $parent);

  /**
   * @param FileInterface $file
   *
   * @return FileInterface
   */
  public function updateFile(FileInterface $file);

  /**
   * @param FileInterface $file
   * @param               $newName
   *
   * @return FileInterface
   */
  public function renameFile(FileInterface $file, $newName);

  /**
   * @param FileInterface $file
   *
   * @return bool
   */
  public function deleteFile(FileInterface $file);

  /**
   * @param FolderInterface $folder
   *
   * @return FolderInterface
   */
  public function createRootFolder(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   * @param FolderInterface $parent
   *
   * @return FolderInterface
   */
  public function createFolder( FolderInterface $folder, FolderInterface $parent );

  /**
   * @param FolderInterface $folder
   *
   * @return bool
   */
  public function deleteFolder(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   * @param                 $newName
   *
   * @return FolderInterface
   */
  public function renameFolder(FolderInterface $folder, $newName);

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getFolderCount(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getFileCount(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getDirectorySize(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   *
   * @return FolderInterface[]
   */
  public function getFolders(FolderInterface $folder);

  /**
   * @param FolderInterface $folder
   *
   * @return FileInterface[]
   */
  public function getFiles(FolderInterface $folder);
}
?>