# php-ftp

PHP FTP Utilities

If you also need SFTP : [php-sftp](https://github.com/hugsbrugs/php-sftp) 

## Install

Install package with composer
```
composer require hugsbrugs/php-ftp
```

In your PHP code, load librairy
```php
require_once __DIR__ . '/vendor/autoload.php';
use Hug\Ftp\Ftp as Ftp;
```

## Usage

Test FTP connection
```php
Ftp::test($server, $user, $password, $port = 21);
```

Check if a file exists on Ftp Server
```php
Ftp::is_file($server, $user, $password, $remote_file, $port = 21);
```

Delete a file on remote FTP server
```php
Ftp::delete($server, $user, $password, $remote_file, $port = 21);
```

Recursively deletes files and folder in given directory (If remote_path ends with a slash delete folder content otherwise delete folder itself)
```php
Ftp::rmdir($server, $user, $password, $remote_path, $port = 21);
```

Recursively copy files and folders on remote FTP server (If local_path ends with a slash upload folder content otherwise upload folder itself)
```php
Ftp::upload_dir($server, $user, $password, $local_path, $remote_path, $port = 21);
```

Download a file from remote Ftp server
```php
Ftp::download($server, $user, $password, $remote_file, $local_file, $port = 21);
```

Download a directory from remote FTP server (If remote_dir ends with a slash download folder content otherwise download folder itself)
```php
Ftp::download_dir($server, $user, $password, $remote_dir, $local_dir, 
$port = 21);
```

Rename a file on remote FTP server
```php
Ftp::rename($server, $user, $password, $old_file, $new_file, $port = 21);
```

Create a directory on remote FTP server
```php
Ftp::mkdir($server, $user, $password, $directory, $port = 21);
```

Create a file on remote FTP server
```php
Ftp::touch($server, $user, $password, $remote_file, $content, $port = 21);
```

Upload a file on FTP server
```php
Ftp::upload($server, $user, $password, $local_file, $remote_file = '', $port = 21);
```

List files on FTP server
```php
Ftp::scandir($server, $user, $password, $path, $port = 21);
```

Get default login FTP directory aka pwd
```php
Ftp::pwd($server, $user, $password, $port = 21);
```

## Tests

Edit example/test.php with your FTP parameters then run 
```php
php example/test.php
```

## To Do

PHPUnit Tests

## Author

Hugo Maugey [visit my website ;)](https://hugo.maugey.fr)