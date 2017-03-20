<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 0);

require_once __DIR__ . '/../vendor/autoload.php';

use Hug\Ftp\Ftp as Ftp;


$FtpServer = 'ip or hostname';
$FtpUser = 'username';
$FtpPass = 'password';
$FtpPath = 'public_html';



#
$test = Ftp::scandir($FtpServer, $FtpUser, $FtpPass, $FtpPath);
echo "scandir\n";
var_dump($test);

#
$test = Ftp::test($FtpServer, $FtpUser, $FtpPass);
echo "Test\n";
var_dump($test);

# Check Login Directory PWD
$test = Ftp::pwd($FtpServer, $FtpUser, $FtpPass);
echo "pwd\n";
var_dump($test);



# Upload File
$local_file = __DIR__ . '/test.txt';
$remote_file = $FtpPath . '/test.txt';
$test = Ftp::upload($FtpServer, $FtpUser, $FtpPass, $local_file, $remote_file);
echo "upload\n";
var_dump($test);

# Download File
$local_file = __DIR__ . '/test-download.txt';
$remote_file = $FtpPath . '/test.txt';
$test = Ftp::download($FtpServer, $FtpUser, $FtpPass, $remote_file, $local_file);
echo "download\n";
var_dump($test);
unlink($local_file);

# Rename File
$old_file = $FtpPath . '/test.txt';
$new_file = $FtpPath . '/test-renamed.txt';
$test = Ftp::rename($FtpServer, $FtpUser, $FtpPass, $old_file, $new_file);
echo "rename\n";
var_dump($test);

# Test file exist
$remote_file = $FtpPath . '/test-renamed.txt';
$test = Ftp::is_file($FtpServer, $FtpUser, $FtpPass, $remote_file);
echo "is_file\n";
var_dump($test);

# Delete File
$remote_file = $FtpPath . '/test-renamed.txt';
$test = Ftp::delete($FtpServer, $FtpUser, $FtpPass, $remote_file);
echo "delete\n";
var_dump($test);



# Upload Folder
// if ends with a slash upload content
// if no slash end upload dir itself
$local_path = __DIR__ . '/../src/';
$remote_path = $FtpPath . '/';
$test = Ftp::upload_dir($FtpServer, $FtpUser, $FtpPass, $local_path, $remote_path);
echo "upload_dir\n";
var_dump($test);

# Download Folder
// if ends with a slash download content
// if no slash end download dir itself
$remote_dir = '/' . $FtpPath . '/src/';
$local_dir = __DIR__;
$test = Ftp::download_dir($FtpServer, $FtpUser, $FtpPass, $remote_dir, $local_dir);
echo "download_dir\n";
var_dump($test);

# Delete Folder
// if ends with a slash delete content
// if no slash delete dir itself
$remote_path = '/'.$FtpPath . '/Hug';
$test = Ftp::rmdir($FtpServer, $FtpUser, $FtpPass, $remote_path);
echo "rmdir\n";
var_dump($test);



# Create File
$file_name = $FtpPath . '/test.txt';
$file_content = 'Love it !';
$test = Ftp::touch($FtpServer, $FtpUser, $FtpPass, $file_name, $file_content);
echo "touch\n";
var_dump($test);

# Delete File
$remote_file = $FtpPath . '/test.txt';
$test = Ftp::delete($FtpServer, $FtpUser, $FtpPass, $remote_file);
echo "delete\n";
var_dump($test);



# Create Folder
$directory = '/' . $FtpPath . '/coucou';
$test = Ftp::mkdir($FtpServer, $FtpUser, $FtpPass, $directory);
echo "mkdir\n";
var_dump($test);

# Rename Folder
$old_file = $FtpPath . '/coucou';
$new_file = $FtpPath . '/coco';
$test = Ftp::rename($FtpServer, $FtpUser, $FtpPass, $old_file, $new_file);
echo "rename";
var_dump($test);

# Delete Folder
$remote_path = $FtpPath . '/coco';
$test = Ftp::rmdir($FtpServer, $FtpUser, $FtpPass, $remote_path);
echo "rmdir\n";
var_dump($test);


$test = Ftp::scandir($FtpServer, $FtpUser, $FtpPass, $FtpPath);
echo "scandir";
var_dump($test);
