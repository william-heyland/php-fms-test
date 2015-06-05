<?php
/* Give the project it's own namespace. */
namespace FMS;

/* Import the global class Exception into our namespace */
use Exception;

/* Import the global class RuntimeException into our namespace */
use RuntimeException;

if (!defined('FMS\SECURED') ) throw new RuntimeException('Attempted security breach');

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
 * @param multi $input The data to be validated
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
      throw new Exception('Missing input field: '.var_export($field_spec, true).'.', INVALID_INPUT ); 
    }

    return NULL;
  }

  switch($field_spec['type'])
  {
    case 'array':
      if(!is_array($input))
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'numeric':
      if(!is_numeric($input) || (isset($field_spec['maxvalue']) && $input > $field_spec['maxvalue']) || (isset($field_spec['minvalue']) && $input < $field_spec['minvalue']))
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'integer':
      if( !ctype_digit($input) || (isset($field_spec['maxvalue']) && $input > $field_spec['maxvalue']) || (isset($field_spec['minvalue']) && $input < $field_spec['minvalue']))
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'date':
      if ( !preg_match( PERL_REGEX_DATETIME_VALIDATION, $input )  )
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'foldername':
      /* Limit filenames to alphanumeric characters, '-', and '_'. Limit byte length to 256 bytes. */
      if ( !preg_match( PERL_REGEX_FOLDERNAME_VALIDATION, $input ) || mb_strlen($input, '8bit') > 256 )
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'filename':
      /* Limit filenames to alphanumeric characters, '-', and '_'. Limit byte length to 256 bytes. */
      if ( !preg_match( PERL_REGEX_FILENAME_VALIDATION, $input ) || mb_strlen($input, '8bit') > 256 )
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'path':
      /* Limit path to alphanumeric characters, '-', '_', and '/'. Limit byte length to 8192 bytes. */
      if ( !preg_match( PERL_REGEX_PATH_VALIDATION, $input ) || mb_strlen($input, '8bit') > 8192 )
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    case 'text':
      if( isset( $field_spec['maxlength'] ) && $field_spec['maxlength'] > 0 && $field_spec['maxlength'] < strlen($input) || isset( $field_spec['minlength'] ) && $field_spec['minlength'] > 0 && $field_spec['minlength'] > strlen($input) )
      {
        throw new Exception('Invalid input ('.$field_spec['type'].'): '.var_export($input, true).'.', INVALID_INPUT ); 
      }
    break;
    default:
      throw new RuntimeException('Unrecognised field type ('.$field_spec['type'].'): '.var_export($input, true), INVALID_INPUT ); 
    break;
  }

  return $input;
}

?>