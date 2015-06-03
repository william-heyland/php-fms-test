<?php
/**
 * CLI to the File Management System test
 *
 * This CLI is modelled on the interactive FTP interface
 * Supported interactive commands include:
 * 'bye', 'cd', 'delete', 'dir', 'exit', 'get', 'help', 'lcd', 'lpwd', 'ls', 'mkdir', 'moo', 'put', 'pwd', 'quit', 'rename', 'rmdir', 'size'
 */

/**
 * SECURED
 *
 * The purpose of the SECURED constant is to prevent direct calls to other php scripts in this project.
 * The following condition should be present at the top of every file in this project:
 * if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);
 */
define( 'SECURED', true );

/* Include the cli configuration */
require_once('/home/jdi/php-fms-test/cli/config/config.inc');

/* Include the cli command handlers */
require_once('/home/jdi/php-fms-test/cli/commands.php');

/* Retrieve command line parameters */
$command_line_parameters = getopt( $short_options, $long_options );

/* Extract list of options */
$options = array_keys( $command_line_parameters );

/* Process command line options. */
foreach( $options as $option )
{
  switch( $option )
  {
    case 'h':
    case 'help':
      /* Display the in-built help and exit */
      fmsCliHelp();
      exit();
    break;
    case 'moo':
      /* Display a cow and exit */
      fmsCliMoo();
      exit();
    break;
  }
}

/* Include the File Management System */
require_once(ROOT_PATH.'system/FileManagementSystem.php');

/* Instantiate the filesystem API */
$filesystem = new \FMS\FileSystem();

/**
 * @var Folder $fmswd The current working directory on the FMS. Defaults to root folder.
 */
$fmswd = $filesystem->getRootFolder();

/* Launch interactive mode */
waitForInput();

/**
 * Wait for user input
 */
function waitForInput()
{
  displayPrompt();

  /* Retrieve and remove erroneous white space around user input. */
  $input = trim(fgets(STDIN));

  try
  {
    processInput( $input );
  }
  catch (Exception $e)
  {
    displayExceptionErrorMessage( $e );
    waitForInput();
  }
}

/**
 * Process a line of user input
 */
function processInput($input)
{
  global $command_map;

  /* Tokenise the $input on white space ' ' */
  $parameters = explode( ' ', $input );

  if ( !isset( $parameters[0] ) || empty( $parameters[0] ) )
    throw new Exception( 'Missing command.', CLI_MISSING_INPUT ); 
  
  if ( !array_key_exists( $parameters[0], $command_map ) )
    throw new Exception('Unknown command.', CLI_INVALID_INPUT);

  /* Call command function handler */
  $command_map[$parameters[0]]( $parameters );

  /* Wait for more input */
  waitForInput();
}

function displayExceptionErrorMessage( $e )
{
  $error_message = $e->getMessage().' Type "help" for a full command synopsis.';
  
  displayOutput( $error_message );
}

/**
 * Display the FMS interactive command prompt
 */
function displayPrompt()
{
  global $fmswd;

  fputs(STDOUT, 'FMS:'.$fmswd->getPath().'> ');
}

/**
 * Display output
 *
 * @param string $string Display string on STDOUT
 */
function displayOutput( $string )
{
  fputs(STDOUT, PHP_EOL.PHP_EOL.$string.PHP_EOL.PHP_EOL);
}

?> 
