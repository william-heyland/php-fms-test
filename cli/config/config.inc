<?php

if (!defined('SECURED') ) throw new Exception('Possible attempted security breach', SECURITY_ALERT);

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
'rename' => 'fmsCliRename',
'rmdir' => 'fmsCliRmdir',
'size' => 'fmsCliSize'
);

/**
 * @var string $help A full cli command synopsis
 */
$help = <<<EOH
NAME
      FMS — File Management System (CLI)
     
SYNOPSIS
      php cli.php -h --help

DESCRIPTION
      cli.php is a command line interface to a database driven, File Management System. The program runs in interactive mode.

      Recognised command line options:
      
          -h
          --help
                  Display this help screen
      
      In interactive mode, cli.php supports a subset of commands found in FTP including:
      
          bye
                  Terminate the FMS session.
          
          cd "FMS directory"
                  Change the FMS working directory.
          
          delete "FMS file"
                  Delete an FMS file.
          
          dir
                  List the contents of current FMS working directory.
          
          exit
                  A synonym for bye.
          
          get "FMS file"
                  Retrieve a file from the FMS into the current LOCAL working directory.
          
          help
                  Display this help screen.
          
          lcd
                  Change the LOCAL working directory.
          
          lpwd
                  Print the path of the current LOCAL working directory.
          
          ls
                  A synonym for dir.
          
          mkdir "FMS directory name"
                  Create a new directory under the current FMS working directory.
          
          put "LOCAL file"
                  Upload a local file to the current FMS working directory.
          
          pwd
                  Print the path of the current FMS working directory.
          
          quit
                  A synonym for dir.
          
          rename "FMS filename" "new filename"
                  Rename a FMS file.
          
          rmdir "FMS folder"
                  Delete a FMS folder.
          
          size "FMS file" | "FMS folder"
                  Give size of FMS file or folder
EOH;

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