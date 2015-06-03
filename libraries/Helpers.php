<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

if (!defined('FMS\SECURED') ) throw new Exception('Attempted security breach', SECURITY_ALERT);

/**
 * The validateInput function to handle all input validation
 *
 * $field_spec is and array in the following format:
 * $field_spec = array(
 *  'required' => 'true|false',
 *  'default_value' => mixed,
 *  'type' => 'array|numeric|integer|text|date',
 *  'maxvalue' => int,
 *  'minvalue' => int,
 *  'maxlength' => int,
 *  'minlength' => int
 * );
 *
 * @param multi $input The database to be validated
 * @param array $field_spec The format of the data to be validated
 *
 * @return array Returns array of normalised input values
 */
function validateInput($input,array $field_spec)
{
  /* Trim the input if it is a string */
  if(!is_string($input))
    $input = trim( $input );

  /* Is the input empty? */
  if( empty($input) && $input !== 0 && $input !== '0' )
  {
    /* Have we requested a default value? */
    if( isset( $field_spec['default_value'] ) && !empty( $field_spec['default_value'] ) )
    {
      return $field_spec['default_value'];
    }

    /* Is this a required field? */
    if( $field_spec['required'] == 'true' && empty($input) && $input !== 0 && $input !== '0' )
    {
      throw new Exception('Missing input field: '.var_export($field_spec, true), INVALID_INPUT ); 
    }

    return NULL;
  }

  switch($field_spec['type'])
  {
    case 'array':
      if(!is_array($input))
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'numeric':
      if(!is_numeric($input) || (isset($field_spec['maxvalue']) && $input > $field_spec['maxvalue']) || (isset($field_spec['minvalue']) && $input < $field_spec['minvalue']))
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'integer':
      if( !ctype_digit($input) || (isset($field_spec['maxvalue']) && $input > $field_spec['maxvalue']) || (isset($field_spec['minvalue']) && $input < $field_spec['minvalue']))
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'date':
      if ( !preg_match( PERL_REGEX_DATETIME_VALIDATION, $input )  )
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'foldername':
      /* Limit filenames to alphanumeric characters, '-', '_', and spaces. Limit byte length to 256 bytes. */
      if ( !preg_match( PERL_REGEX_FOLDERNAME_VALIDATION, $input ) || mb_strlen($input, '8bit') > 256 )
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'filename':
      /* Limit filenames to alphanumeric characters, '-', '_', and spaces. Limit byte length to 256 bytes. */
      if ( !preg_match( PERL_REGEX_FILENAME_VALIDATION, $input ) || mb_strlen($input, '8bit') > 256 )
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'path':
      /* Limit path to alphanumeric characters, '-', '_', '/', and spaces. Limit byte length to 8192 bytes. */
      if ( !preg_match( PERL_REGEX_PATH_VALIDATION, $input ) || mb_strlen($input, '8bit') > 8192 )
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'text':
      if( isset( $field_spec['maxlength'] ) && $field_spec['maxlength'] > 0 && $field_spec['maxlength'] < strlen($input) || isset( $field_spec['minlength'] ) && $field_spec['minlength'] > 0 && $field_spec['minlength'] > strlen($input) )
      {
        throw new Exception('Invalid input: '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    default:
      throw new Exception('Unrecognised field spec: '.var_export($input, true), INVALID_INPUT ); 
    break;
  }

  return $input;
}

/**
 * Variables defined in global scope used to construct and display the filesystem for debugging and testing purposes
 */
$sorted_folders = array();
$sorted_files = array();

/**
 * Display filesystem
 *
 * A simple text formatted display of the filesystem for debugging and testing purposes
 */
function displayFilesystem( $database )
{
  global $sorted_folders, $sorted_files;
  $sorted_folders = array();
  $sorted_files = array();

  $folders = $database->table('folders')->select();
  $files = $database->table('files')->select();
  
  /* Sort and Index arrays of folders by parent_folder_id. Sort root folders in special 'root' key. */
  foreach( $folders as $folder )
  {
    /* Is this a root folder? */
    if ( $folder['path'] == '/' )
    {
      if ( !isset( $sorted_folders['/'] ) )
        $sorted_folders['/'] = array();

      $sorted_folders['/'][] = $folder;
      continue;
    }

    if ( !isset( $sorted_folders[$folder['parent_folder_id']] ) )
      $sorted_folders[$folder['parent_folder_id']] = array();
    
    $sorted_folders[$folder['parent_folder_id']][] = $folder;
  }

  /* Sort and Index arrays of files by parent_folder_id */
  foreach( $files as $file )
  {
    if ( !isset( $sorted_files[$file['parent_folder_id']] ) )
      $sorted_files[$file['parent_folder_id']] = array();
    
    $sorted_files[$file['parent_folder_id']][] = $file;
  }

  displayFolders( '/', 0 );
  echo PHP_EOL;
}

function displayFolders( $index, $level )
{
  global $sorted_folders, $sorted_files;

  if ( !isset( $sorted_folders[$index] ) )
  {
    return false;
  }
  foreach( $sorted_folders[$index] as $folder )
  {
    /* Output folder */
    echo str_repeat( '  ', $level ).'==> ';
    echo $folder['name'];
    echo PHP_EOL;

    $level++;
    displayFiles( $folder['folder_id'], $level );
    displayFolders( $folder['folder_id'], $level );
  }
}

function displayFiles( $index, $level )
{
  global $sorted_folders, $sorted_files;

  if ( !isset( $sorted_files[$index] ) )
  {
    return false;
  }
  foreach( $sorted_files[$index] as $file )
  {
    /* Output file */
    echo str_repeat( '  ', $level ).'--  ';
    echo $file['name'];
    echo PHP_EOL;
  }
}

?>