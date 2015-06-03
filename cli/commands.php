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
    throw new Exception('Missing command parameter "folder name".', CLI_MISSING_INPUT);

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
    $folder_names .= $folder->getName().PHP_EOL;
  }

  /* Optain is list of files within the current FMS working directory */
  $files = $filesystem->getFiles( $fmswd );
  
  $file_names = '';
  
  foreach( $files as $file )
  {
    $file_names .= $file->getName().PHP_EOL;
  }

  displayOutput( 'Folders: '.PHP_EOL.'========'.PHP_EOL.$folder_names.PHP_EOL.'Files:'.PHP_EOL.'======'.PHP_EOL.$file_names );

  return true;
}

/**
 * Retrieve a file from the FMS into the current LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliGet( $parameters = array() )
{
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
 * 
 *
 * @param Array $parameters
 */
function fmsCliLdir( $parameters = array() )
{
  /* Use file globbing to retrieve LOCAL directories */
  if ( ! $folders = $dirs = array_filter(glob('*'), 'is_dir') )
    throw new Exception( 'Unknown LOCAL directory.', CLI_INVALID_INPUT );

  $folder_names = '';
  foreach( $folders as $folder )
  {
    $folder_names .= $folder.PHP_EOL;
  }
  
  /* Use file globbing to retrieve LOCAL files */
  if ( ! $files = $dirs = array_filter(glob('*'), 'is_file') )
    throw new Exception( 'Unknown LOCAL directory.', CLI_INVALID_INPUT );

  $file_names = '';
  foreach( $files as $file )
  {
    $file_names .= $file.PHP_EOL;
  }

  displayOutput( 'Folders: '.PHP_EOL.'========'.PHP_EOL.$folder_names.PHP_EOL.'Files:'.PHP_EOL.'======'.PHP_EOL.$file_names );

  return true;
}

/**
 * Print the path of the current LOCAL working directory.
 *
 * @param Array $parameters
 */
function fmsCliLpwd( $parameters = array() )
{
  displayOutput( getcwd() );

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
    throw new Exception('Missing command parameter "folder name".', CLI_MISSING_INPUT);

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

  displayOutput( $cow );

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
    throw new Exception('Missing command parameter "LOCAL file name".', CLI_MISSING_INPUT);

  /* Check that the local file exists */
  if ( !is_file( getcwd().'/'.$parameters[1] ) )
    throw new Exception('Unknown local file "'.getcwd().'/'.$parameters[1].'".', CLI_INVALID_INPUT);

  /* Create a new File instance */
  $file = new FMS\File();

  /* Set the file Name */
  $file->setName( $parameters[1] );

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

  displayOutput( $fmswd->getPath() );

  return true;
}

/**
 * Rename an FMS file.
 *
 * @param Array $parameters
 */
function fmsCliRename( $parameters = array() )
{
  global $filesystem;
  global $fmswd;

  /* Check that the user has supplied a FMS filename */
  if ( !isset( $parameters[1] ) )
    throw new Exception('Missing command parameter "FMS filename".', CLI_MISSING_INPUT);

  /* Check that the user has supplied a new FMS filename */
  if ( !isset( $parameters[2] ) )
    throw new Exception('Missing command parameter "new filename".', CLI_MISSING_INPUT);

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
    throw new Exception('Missing command parameter "folder name".', CLI_MISSING_INPUT);

  /* Optain is list of folders within the current working directory */
  $folders = $filesystem->getFolders( $fmswd );

  /* Identify which folder we are attempting to delete */
  $folder_to_delete;
  foreach( $folders as $folder )
  {
    if ( $folder->getName() == $parameters[1] )
      $folder_to_delete = $folder;
  }
  
  $filesystem->deleteFolder( $folder );

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