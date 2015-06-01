<?php
/**
 * Test API calls
 *
 * Warning: This file contains extensive calls to the File, Folder, and FileSystem api.
 * After each operation, the filesystem is displayed.
 * This file is intended to be called directly from the command line for testing purposes.
 */

/* Include core index.php file */
require_once('/home/jdi/php-fms-test/index.php');


/* Clear all existing data from the database */
$database->table('files')->emptyTable();
$database->table('folders')->emptyTable();


/* Create a new root folder */
echo "Create new 'root folder'".PHP_EOL;
$root_folder = new Folder;
$root_folder->setName('root folder');
$filesystem->createRootFolder( $root_folder );

/* Display filesystem summary */
displayFilesystem( $database );


/* Create a new sub-folder */
echo "Create new 'sub-folder'".PHP_EOL;
$sub_folder = new Folder;
$sub_folder->setName('sub-folder');
$filesystem->createFolder( $sub_folder, $root_folder );  

/* Display filesystem summary */
displayFilesystem( $database );


/* Create new file under the root folder */
echo "Create 'first file' under 'root folder'".PHP_EOL;
$first_file = new File;
$first_file
  ->setName('first file')
  ->setSize(1024);
$filesystem->createFile( $first_file, $root_folder );

/* Display filesystem summary */
displayFilesystem( $database );


/* Echo file count under 'root folder' */
$count = $filesystem->getFileCount( $root_folder );
echo "Root folder file count: $count".PHP_EOL.PHP_EOL;

/* Echo size of 'root folder' */
$count = $filesystem->getDirectorySize( $root_folder );
echo "Root folder size: $count".PHP_EOL.PHP_EOL;


/* Echo file count under 'sub-folder' */
$count = $filesystem->getFileCount( $sub_folder );
echo "Sub-folder file count: $count".PHP_EOL.PHP_EOL;

/* Echo size of 'sub-folder' */
$count = $filesystem->getDirectorySize( $sub_folder );
echo "Sub-folder size: $count".PHP_EOL.PHP_EOL;


/* Create a new file under the sub-folder */
echo "Create 'second file' under 'sub-folder'".PHP_EOL;
$second_file = new File;
$second_file
  ->setName('second file')
  ->setSize(2048);
$filesystem->createFile( $second_file, $sub_folder );

/* Display filesystem summary */
displayFilesystem( $database );


/* Print list of folders under 'root folder' */
$folders = $filesystem->getFolders( $root_folder );
echo "Folders under root: ".var_dump( $folders, true ).PHP_EOL.PHP_EOL;

/* Print list of files under 'root folder' */
$files = $filesystem->getFiles( $root_folder );
echo "Files under root: ".var_dump( $files, true ).PHP_EOL.PHP_EOL;


/* Rename $second_file to 'renamed second file' */
echo "Rename 'second file' to 'renamed second file'".PHP_EOL;
$filesystem->renameFile( $second_file, 'renamed second file' );

/* Display filesystem summary */
displayFilesystem( $database );


/* Update $first_file size, and set new parent folder (move file) */
echo "Move 'first file' to 'sub-folder'".PHP_EOL;
$first_file
  ->setSize(4096)
  ->setParentFolder( $sub_folder );
$filesystem->updateFile( $first_file );

/* Display filesystem summary */
displayFilesystem( $database );


/* Echo size of 'sub-folder' */
$count = $filesystem->getDirectorySize( $sub_folder );
echo "Sub-folder size: $count".PHP_EOL.PHP_EOL;


/* Delete $second_file */
echo "Delete 'renamed second file'".PHP_EOL;
$filesystem->deleteFile( $second_file );

/* Display filesystem summary */
displayFilesystem( $database );


/* Rename 'sub-folder' to 'renamed sub-folder' */
echo "Rename 'sub-folder' to 'renamed sub-folder'".PHP_EOL;
$filesystem->renameFolder( $sub_folder, 'renamed sub-folder' );

/* Display filesystem summary */
displayFilesystem( $database );


/* Echo folder count under 'root folder' */
$count = $filesystem->getFolderCount( $root_folder );
echo "Root folder count: $count".PHP_EOL.PHP_EOL;


/* Echo folder count under $sub_folder */
$count = $filesystem->getFolderCount( $sub_folder );
echo "Sub-folder count: $count".PHP_EOL.PHP_EOL;


/* Delete 'renamed sub-folder' */
echo "Delete 'renamed sub-folder'".PHP_EOL;
$filesystem->deleteFolder( $sub_folder );

/* Display filesystem summary */
displayFilesystem( $database );




?>