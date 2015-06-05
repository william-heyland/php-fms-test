<?php

if (!defined('SECURED') ) throw new RuntimeException('Possible attempted security breach');

/**
 * ROOT_PATH
 *
 * The full path to the FMS installation folder
 */
define( 'ROOT_PATH', '/home/jdi/php-fms-test/' );

/* supported CLI options */
$short_options = 'h';
$long_options = array(
  'help',
  'moo'
);

/**
 * @var array $command_map A full list of supported commands and their function mappings
 */
$command_map = array(
'bye' => 'fmsCliExit',
'cd' => 'fmsCliCd',
'delete' => 'fmsCliDelete',
'dir' => 'fmsCliDir',
'exit' => 'fmsCliExit',
'get' => 'fmsCliGet',
'help' => 'fmsCliHelp',
'lcd' => 'fmsCliLcd',
'ldir' => 'fmsCliLdir',
'lpwd' => 'fmsCliLpwd',
'ls' => 'fmsCliDir',
'mkdir' => 'fmlsCliMkdir',
'moo' => 'fmsCliMoo',
'put' => 'fmsCliPut',
'pwd' => 'fmsCliPwd',
'quit' => 'fmsCliExit',
'rename' => 'fmsCliRenameFile',
'renamedir' => 'fmsCliRenameFolder',
'rmdir' => 'fmsCliRmdir',
'size' => 'fmsCliFileSize'
);

/* Some useful Linux console escape sequences used in help hereDoc below */
$bold = getConsoleEscapeSequence('bold');
$normal = getConsoleEscapeSequence('normal');

/**
 * @var string $help A full cli command synopsis
 */
$help = <<<EOH
${bold}NAME${normal}
      FMS — File Management System (CLI)
     
${bold}SYNOPSIS${normal}
      php cli.php -h --help

${bold}DESCRIPTION${normal}
      cli.php is a command line interface to a database driven, File Management System. The program runs in interactive mode.

      Recognised command line options:

          ${bold}-h${normal}
          ${bold}--help${normal}
                  Display this help screen
      
      In interactive mode, cli.php supports a subset of commands found in FTP including:

          ${bold}bye${normal}
                  Terminate the FMS session.

          ${bold}cd${normal} "FMS directory name"
                  Change the FMS working directory.

          ${bold}delete${normal} "FMS filename"
                  Delete an FMS file.

          ${bold}dir${normal}
                  List the contents of current FMS working directory.

          ${bold}exit${normal}
                  A synonym for bye.

          ${bold}get${normal} "FMS filename"
                  Retrieve a file from the FMS into the current LOCAL working directory.

          ${bold}help${normal}
                  Display this help screen.

          ${bold}lcd${normal}
                  Change the LOCAL working directory.
                  
          ${bold}ldir${normal}
                  List contents of current LOCAL working directory.

          ${bold}lpwd${normal}
                  Print the path of the current LOCAL working directory.

          ${bold}ls${normal}
                  A synonym for dir.

          ${bold}mkdir${normal} "FMS directory name"
                  Create a new directory under the current FMS working directory.

          ${bold}put${normal} "LOCAL filename"
                  Upload a local file to the current FMS working directory.

          ${bold}pwd${normal}
                  Print the path of the current FMS working directory.

          ${bold}quit${normal}
                  A synonym for dir.

          ${bold}rename${normal} "FMS filename" "New filename"
                  Rename a FMS file.

          ${bold}renamedir${normal} "FMS directory name" "New directory name"
                  Rename a FMS file.

          ${bold}rmdir${normal} "FMS directory name"
                  Delete a FMS directory.

          ${bold}size${normal} "FMS filename"
                  Give size of a FMS file
EOH;

/**
 * @var string $welcome_message A nice friendly welcome message
 */
$welcome_message = <<<EOT
Welcome to the interactive FMS command interface

Type "help" for a full command SYNOPSIS. If you are familiar with command line FTP, you should feel right at home here since I have modelled this interface on the same command set.
EOT;

/**
 * @var string $cow Unable to explain this
 */
$cow = <<<EOC
             (    )
     #        (oo)
      \.-----/(O O)
     =;       / U
    = ( )._.( )
      |/   |/
  === " '  " '
EOC;

/**
 * Common error codes used in my exception handling
 */
define( 'CLI_INVALID_INPUT', 1 );
define( 'CLI_MISSING_INPUT', 2 );

?>