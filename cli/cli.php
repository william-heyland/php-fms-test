<?php
/**
 * CLI to the File Management System test
 *
 * This CLI is modelled on the interactive FTP interface
 * Supported interactive commands include:
 * 'bye', 'cd', 'delete', 'dir', 'exit', 'get', 'help', 'lcd', 'ldir', 'lpwd', 'ls', 'mkdir', 'moo', 'put', 'pwd', 'quit', 'rename', 'renamedir', 'rmdir', 'size'
 */

/**
 * SECURED
 *
 * The purpose of the SECURED constant is to prevent direct calls to other php scripts in this project.
 * The following condition should be present at the top of every file in this project:
 * if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);
 */
define( 'SECURED', true );

/* Toggle error reporting */
error_reporting(0);

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
displayWelcomeMessage();
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
    /* This is our TOP level exception handler */
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
  $error_message = getConsoleEscapeSequence('red').$e->getMessage().getConsoleEscapeSequence('normal').' Type "help" for a full command synopsis.';
  
  displayOutput( $error_message );
}

/**
 * Display the FMS interactive command prompt
 */
function displayPrompt()
{
  global $fmswd;

  $prompt = 'FMS:'.getConsoleEscapeSequence('blue').$fmswd->getPath().getConsoleEscapeSequence('red').'> '.getConsoleEscapeSequence('normal');

  fputs(STDOUT, $prompt);
}

/**
 * Display welcome message
 */
function displayWelcomeMessage()
{
  global $welcome_message;
  
  $welcome_message = getConsoleEscapeSequence('yellow').$welcome_message.getConsoleEscapeSequence('normal');
  
  displayOutput( $welcome_message );
}

/**
 * Display output
 *
 * @param string $string Display string on STDOUT
 */
function displayOutput( $string )
{
  fputs(STDOUT, PHP_EOL.$string.PHP_EOL.PHP_EOL);
}

/**
 * Get console escape sequence (Linux only I'm afraid!)
 */
function getConsoleEscapeSequence( $sequence_name )
{
  if ( PHP_OS != 'Linux' )
    return '';

  $escape_sequences = array( 
    'normal'     => "[0m", 
    'red'         => "[1;31m", 
    'green'     => "[1;32m", 
    'yellow'     => "[0;33m", 
    'blue'         => "[1;34m", 
    'bold'         => "[1m", 
    'underscore'     => "[4m", 
  );

  if ( array_key_exists( $sequence_name, $escape_sequences ) )
    return chr(27).$escape_sequences[$sequence_name];
    
  return '';
}
?>