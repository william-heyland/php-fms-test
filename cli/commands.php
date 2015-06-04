<?php

if (!defined('SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

/*
 * Function definitions of all supported command handlers.
 */

/**
 * Terminate the FMS session.
 *
 * @param Array $parameters
 */
function fmsCliExit( $parameters = array() )
{
  displayOutput('Bye');
  exit();
}

/**
 * Change the FMS working directory.
 *
 * @param Array $parameters
 */
function fmsCliCd( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a folder name */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS directory name".', CLI_MISSING_INPUT);

  /* Check for special path '/' */
  if ( $parameters[1] == '/' )
  {
    if ( $root_folder = $filesystem->getRootFolder() )
      $fmswd = $root_folder;

    return true;
  }
  
  /* Check for special path '../' */
  if ( $parameters[1] == '../' )
  {
    if ( $parent_folder = $filesystem->getParentFolder( $fmswd ) )
      $fmswd = $parent_folder;

    return true;
  }

  /* Obtain a list of folders within the current working directory */
  $folders = $filesystem->getFolders( $fmswd );

  /* Identify which folder we are attempting to move to */
  $folder_to_move_to = NULL;
  foreach( $folders as $folder )
  {
    if ( $folder->getName() == $parameters[1] )
    {
      $folder_to_move_to = $folder;
      break;
    }
  }
  
  if ( empty( $folder_to_move_to ) )
    throw new Exception( 'Unknown directory.', CLI_INVALID_INPUT );

  /* Change the current working directory within the FMS */
  $fmswd = $folder_to_move_to;

  return true;
}

/**
 * Delete an FMS file.
 *
 * @param Array $parameters
 */
function fmsCliDelete( $parameters = array() )
{
  global $filesystem;
  global $fmswd;
  
  /* Check that the user has supplied a filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS filename".', CLI_MISSING_INPUT);

  /* Optain is list of files within the current working directory */
  $files = $filesystem->getFiles( $fmswd );

  /* Identify which file we are attempting to delete */
  $file_to_delete;
  foreach( $files as $file )
  {
    if ( $file->getName() == $parameters[1] )
      $file_to_delete = $file;
  }
  
  $filesystem->deleteFile( $file_to_delete );

  return true;
}

/**
 * List contents of current FMS working directory.
 *
 * @param Array $parameters
 */
function fmsCliDir( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Obtain a list of folders within the current FMS working directory */
  $folders = $filesystem->getFolders( $fmswd );

  $folder_names = '';

  foreach( $folders as $folder )
  {
    $folder_names .= getConsoleEscapeSequence('blue').$folder->getName().getConsoleEscapeSequence('normal').PHP_EOL;
  }
  
  $folder_names = getConsoleEscapeSequence('bold').'FMS Folders ('.count($folders).'): '.getConsoleEscapeSequence('normal').PHP_EOL.$folder_names.PHP_EOL;

  /* Optain is list of files within the current FMS working directory */
  $files = $filesystem->getFiles( $fmswd );
  
  $file_names = '';

  foreach( $files as $file )
  {
    $file_names .= getConsoleEscapeSequence('green').$file->getName().getConsoleEscapeSequence('normal').PHP_EOL;
  }
  
  $file_names = getConsoleEscapeSequence('bold').'FMS Files ('.count($files).'):'.getConsoleEscapeSequence('normal').PHP_EOL.$file_names;

  displayOutput( $folder_names.$file_names );

  return true;
}

/**
 * Retrieve a file from the FMS into the current LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliGet( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a FMS filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS filename".', CLI_MISSING_INPUT);

  /* Obtain a list of files within the current FMS working directory */
  $files = $filesystem->getFiles( $fmswd );

  /* Identify which file we are attempting to get */
  $file_to_get = NULL;
  foreach( $files as $file )
  {
    if ( $file->getName() == $parameters[1] )
    {
      $file_to_get = $file;
      break;
    }
  }

  /* Did we find a matching file? */
  if ( empty( $file_to_get ) )
    throw new Exception( 'Unknown FMS file.', CLI_INVALID_INPUT );

  /* Check that the LOCAL filename is valid and does not already exist */
  if ( file_exists( getcwd().'/'.$parameters[1] ) )
    throw new Exception('A file with this filename already exists in the LOCAL working directory "'.getcwd().'/'.$parameters[1].'".', CLI_INVALID_INPUT);

  /* Copy the FMS file to the LOCAL filesystem */
  if ( !copy( FMS\UPLOADS_PATH.$file_to_get->getFileId(), getcwd().'/'.$parameters[1] ) )
    throw new Exception( 'Failed to move file to the current LOCAL working directory.', FILE_IMPORT_ERROR );
    
  return true;
}

/**
 * Display the help screen.
 *
 * @param Array $parameters
 */
function fmsCliHelp( $parameters = array() )
{
  global $help;

  displayOutput( $help );

  return true;
}

/**
 * Change the LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliLcd( $parameters = array() )
{
  if ( ! chdir( $parameters[1] ) )
    throw new Exception( 'Unknown directory.', CLI_INVALID_INPUT );
  
  return true;
}

/**
 * List contents of current LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliLdir( $parameters = array() )
{
  /* Use file globbing to retrieve LOCAL directories */
  $folders = array_filter(glob('*'), 'is_dir');

  $folder_names = '';
  foreach( $folders as $folder )
  {
    $folder_names .= getConsoleEscapeSequence('blue').$folder.getConsoleEscapeSequence('normal').PHP_EOL;
  }
  
  $folder_names = getConsoleEscapeSequence('bold').'LOCAL Folders ('.count($folders).'): '.getConsoleEscapeSequence('normal').PHP_EOL.$folder_names.PHP_EOL;

  /* Use file globbing to retrieve LOCAL files */
  $files = array_filter(glob('*'), 'is_file');

  $file_names = '';
  foreach( $files as $file )
  {
    $file_names .= getConsoleEscapeSequence('green').$file.getConsoleEscapeSequence('normal').PHP_EOL;
  }
  
  $file_names = getConsoleEscapeSequence('bold').'LOCAL Files ('.count($files).'):'.getConsoleEscapeSequence('normal').PHP_EOL.$file_names;

  displayOutput( $folder_names.$file_names );

  return true;
}

/**
 * Print the path of the current LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliLpwd( $parameters = array() )
{
  displayOutput( getConsoleEscapeSequence('bold').getcwd().getConsoleEscapeSequence('normal') );

  return true;
}

/**
 * Create a new directory under the current FMS working directory.
 *
 * @param Array $parameters
 */
function fmlsCliMkdir( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a folder name */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS directory name".', CLI_MISSING_INPUT);

  /* Create a instance of a new folder */
  $folder = new \FMS\Folder();
  $folder
    ->setName( $parameters[1] );

  /* Create the folder */
  $filesystem->createFolder( $folder, $fmswd );

  return true;
}

/**
 * I can't explain this. It's defies logic.
 *
 * @param Array $parameters
 */
function fmsCliMoo( $parameters = array() )
{
  global $cow;

  displayOutput( getConsoleEscapeSequence('bold').$cow.getConsoleEscapeSequence('normal') );

  return true;
}

/**
 * Upload a local file to the current FMS working directory.
 *
 * @param Array $parameters
 */
function fmsCliPut( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a LOCAL filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "LOCAL filename".', CLI_MISSING_INPUT);

  /* Check that the local file exists */
  if ( !is_file( getcwd().'/'.$parameters[1] ) )
    throw new Exception('Unknown local file "'.getcwd().'/'.$parameters[1].'".', CLI_INVALID_INPUT);

  /* Create a new File instance */
  $file = new FMS\File();

  /* Set the file Name */
  $file
    ->setImportFilePath( getcwd().'/'.$parameters[1] )
    ->setName( $parameters[1] );

  /* Add the file to the FMS */
  $filesystem->createFile( $file, $fmswd );

  return true;
}

/**
 * Print the path of the current FMS working directory.
 *
 * @param Array $parameters
 */
function fmsCliPwd( $parameters = array() )
{
  global $fmswd;

  displayOutput( getConsoleEscapeSequence('bold').$fmswd->getPath().getConsoleEscapeSequence('normal') );

  return true;
}

/**
 * Rename an FMS file.
 *
 * @param Array $parameters
 */
function fmsCliRenameFile( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a FMS filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS filename".', CLI_MISSING_INPUT);

  /* Check that the user has supplied a new FMS filename */
  if ( !isset( $parameters[2] ) )
    throw new Exception('Missing command parameter "New filename".', CLI_MISSING_INPUT);

  /* Obtain a list of files within the current FMS working directory */
  $files = $filesystem->getFiles( $fmswd );

  /* Identify which file we are attempting to rename */
  $file_to_rename = NULL;
  foreach( $files as $file )
  {
    if ( $file->getName() == $parameters[1] )
    {
      $file_to_rename = $file;
      break;
    }
  }

  if ( empty( $file_to_rename ) )
    throw new Exception( 'Unknown FMS file.', CLI_INVALID_INPUT );

  /* Rename the file */
  $filesystem->renameFile( $file_to_rename, $parameters[2] );

  return true;
}

/**
 * Rename an FMS folder.
 *
 * @param Array $parameters
 */
function fmsCliRenameFolder( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a FMS filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS directory name".', CLI_MISSING_INPUT);

  /* Check that the user has supplied a new FMS filename */
  if ( !isset( $parameters[2] ) )
    throw new Exception('Missing command parameter "New directory name".', CLI_MISSING_INPUT);

  /* Obtain a list of folders within the current FMS working directory */
  $folders = $filesystem->getFolders( $fmswd );

  /* Identify which folder we are attempting to rename */
  $folder_to_rename = NULL;
  foreach( $folders as $folder )
  {
    if ( $folder->getName() == $parameters[1] )
    {
      $folder_to_rename = $folder;
      break;
    }
  }

  if ( empty( $folder_to_rename ) )
    throw new Exception( 'Unknown FMS file.', CLI_INVALID_INPUT );

  /* Rename the file */
  $filesystem->renameFolder( $folder_to_rename, $parameters[2] );

  return true;
}

/**
 * Delete a FMS folder.
 *
 * @param Array $parameters
 */
function fmsCliRmdir( $parameters = array() )
{
  global $filesystem;
  global $fmswd;
  
  /* Check that the user has supplied a folder name */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS directory name".', CLI_MISSING_INPUT);

  /* Optain is list of folders within the current working directory */
  $folders = $filesystem->getFolders( $fmswd );

  /* Identify which folder we are attempting to delete */
  $folder_to_delete;
  foreach( $folders as $folder )
  {
    if ( $folder->getName() == $parameters[1] )
    {
      $folder_to_delete = $folder;
      break;
    }
  }
  
  $filesystem->deleteFolder( $folder_to_delete );

  return true;
}

/**
 * Give size of FMS file or folder
 *
 * @param Array $parameters
 */
function fmsCliSize()
{
  return true;
}


?>