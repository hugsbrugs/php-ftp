<?php

namespace Hug\Ftp;

use Hug\HString\HString as HString;

/**
 *
 */
class Ftp
{
    /**
     * Login to FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param int $port
     *
     * @return resource $cid
     *
     */
    private static function login($server, $user, $password, $port = 21)
    {
        $cid = false;

        try
        {
            $cid = ftp_connect($server, $port);
            // $cid = ftp_ssl_connect($server);

            if($cid!==false)
            {
                # Login into FTP server 
                if(ftp_login($cid, $user, $password))
                {
                    # Set the network timeout to 10 seconds 
                    ftp_set_option($cid, FTP_TIMEOUT_SEC, 3000);
                }
                else
                {
                    $cid = false;
                    error_log("Ftp::login : cannot connect as $user"); 
                } 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::login : " . $e->getMessage());
        }

        return $cid;
    }

    /**
     * Test Ftp connection
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param int $port
     *
     * @return bool $test
     *
     */
    public static function test($server, $user, $password, $port = 21)
    {
        $test = false;
        
        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                $test = true;
                ftp_close($cid);    
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::test : " . $e->getMessage());
        }

        return $test;
    }

    /**
     * Test if a directory exist
     *
     * @param string $dir
     * @return bool $is_dir 
     */
    private static function is_dir($dir, $cid) 
    { 
        $is_dir = false;
        
        # Get the current working directory 
        $origin = ftp_pwd($cid); 

        # Attempt to change directory, suppress errors 
        if (@ftp_chdir($cid, $dir)) 
        { 
            # If the directory exists, set back to origin 
            ftp_chdir($cid, $origin);    
            $is_dir = true; 
        } 

        return $is_dir;
    }

    /**
     * Check if a file exists on FTP Server
     * 
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $local_file
     * @param string $remote_file
     * @param int $port
     *
     * @return bool $is_file
     */
    public static function is_file($server, $user, $password, $remote_file, $port = 21)
    {
        $is_file = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                $FileSize = ftp_size($cid, $remote_file);
                if($FileSize!==-1)
                {
                    $is_file = true;
                }
                ftp_close($cid); 
            } 
        }
        catch(Exception $e)
        {
            error_log("Ftp::is_file : " . $e->getMessage());
        }

        return $is_file;
    }

    /**
     * Delete a file on remote FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $remote_file
     * @param int $port
     *
     * @return bool $deleted
     *
     */
    public static function delete($server, $user, $password, $remote_file, $port = 21)
    {
        $deleted = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                if(ftp_size($cid, $remote_file)!==-1)
                {
                    # Delete
                    if(ftp_delete($cid, $remote_file))
                    {
                        $deleted = true;
                    }
                }
                else
                {
                    error_log('The file you are trying to delete does not exist.');
                }
                ftp_close($cid); 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::delete : " . $e->getMessage());
        }

        return $deleted;
    }

    /**
     * Recursively delete files and folder in given directory
     *
     * If remote_path ends with a slash delete folder content
     * otherwise delete folder itself
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $remote_path
     * @param int $port
     *
     * @return bool $deleted
     *
     */
    public static function rmdir($server, $user, $password, $remote_path, $port = 21)
    {
        $deleted = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                # Delete directory content
                if(Ftp::clean_dir($remote_path, $cid))
                {
                    # If remote_path do not ends with /
                    if(!HString::ends_with($remote_path, '/'))
                    {
                        # Delete directory itself
                        if(ftp_rmdir($cid, $remote_path))
                        {
                            $deleted = true;
                        }
                    }
                    else
                    {
                        $deleted = true;
                    }

                }
                ftp_close($cid);
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::rmdir : " . $e->getMessage());
        }

        return $deleted;
    }

    /**
     * Recursively delete files and folder
     *
     * @param string $path
     * @param resource $cid
     *
     * @return bool $clean
     */
    private static function clean_dir($path, $cid)
    {
        $clean = false;

        $to_delete = 0;
        $deleted = 0;

        $list = ftp_nlist($cid, $path);        
        foreach ($list as $element)
        {
            if($element!=='.' && $element!=='..')
            {
                $to_delete++;
                // error_log('element : ' . $element);
                if(FTP::is_dir($element, $cid))
                {
                    # Empty directory
                    Ftp::clean_dir($element, $cid);

                    # Delete empty directory
                    if(ftp_rmdir($cid, $element))
                    {
                        $deleted++;
                    }
                }
                else
                {
                    # Delete file
                    if(ftp_delete($cid, $element))
                    {
                        $deleted++;
                    }
                }
            }
        }
        
        if($deleted===$to_delete)
        {
            $clean = true;   
        }

        return $clean;
    }

    /**
     * Upload a directory from local to remote FTP server
     *
     * If local_path ends with a slash upload folder content
     * otherwise upload folder itself
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $local_path
     * @param string $remote_path
     * @param int $port
     *
     * @return bool $upload
     *
     */
    public static function upload_dir($server, $user, $password, $local_path, $remote_path, $port = 21)
    {
        $upload = false;

        try
        {
            # Remove trailing slash
            $remote_path = rtrim($remote_path, DIRECTORY_SEPARATOR);

            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                # If local_path do not ends with /
                if(!HString::ends_with($local_path, '/'))
                {
                    # Create first level directory on remote filesystem
                    $remote_path = $remote_path . DIRECTORY_SEPARATOR . basename($local_path);
                    @ftp_mkdir($cid, $remote_path);
                }

                if(Ftp::is_dir($remote_path, $cid))
                {
                    $upload = Ftp::upload_all($cid, $local_path, $remote_path);
                }
                ftp_close($cid); 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::upload_dir : " . $e->getMessage());
        }

        return $upload;
    }

    /**
     * Recursively copy files and folders on remote SFTP server
     *
     * @param ressource $cid
     * @param string $local_dir
     * @param string $remote_dir
     *
     * @return bool 
     *
     */
    private static function upload_all($cid, $local_dir, $remote_dir)
    {
        $uploaded_all = false;
        
        try
        {
            # Create remote directory
            if(!Ftp::is_dir($remote_dir, $cid))
            {
                if(!ftp_mkdir($cid, $remote_dir))
                {
                    throw new Exception("Cannot create remote directory.", 1);
                }
            }

            $to_upload = 0;
            $uploaded = 0;

            $d = dir($local_dir); 
            while($file = $d->read())
            {
                # To prevent an infinite loop 
                if ($file != "." && $file != "..")
                {
                    $to_upload++;
                    if (is_dir($local_dir . DIRECTORY_SEPARATOR . $file))
                    {
                        # Upload directory
                        # Recursive part
                        if(Ftp::upload_all($cid, $local_dir . DIRECTORY_SEPARATOR . $file, $remote_dir . DIRECTORY_SEPARATOR . $file))
                        {
                            $uploaded++;
                            error_log('upload dir');
                        }
                    }
                    else
                    { 
                        # Upload file
                        if(ftp_put($cid, $remote_dir . DIRECTORY_SEPARATOR . $file, $local_dir . DIRECTORY_SEPARATOR . $file, FTP_BINARY))
                        {
                            $uploaded++;
                            error_log('upload file');
                        }
                    } 
                }
            } 
            $d->close();

            if($to_upload===$uploaded)
            {
                $uploaded_all = true;
            }

        }
        catch(Exception $e)
        {
            error_log("Ftp::upload_all : " . $e->getMessage());
        }

        return $uploaded_all;
    }

    /**
     * Download a file from remote FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $remote_file
     * @param string $local_file
     * @param int $port
     *
     * @return bool $downloaded
     *
     */
    public static function download($server, $user, $password, $remote_file, $local_file, $port = 21)
    {
        $downloaded = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                # Download File
                if (ftp_get($cid, $local_file, $remote_file, FTP_BINARY, 0))
                {
                    $downloaded = true;
                }
                ftp_close($cid); 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::download : " . $e->getMessage());
        }

        return $downloaded;
    }

    /**
     * Download a directory from remote FTP server
     *
     * If remote_dir ends with a slash download folder content
     * otherwise download folder itself
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $remote_dir
     * @param string $local_dir
     * @param int $port
     *
     * @return bool $downloaded
     *
     */
    public static function download_dir($server, $user, $password, $remote_dir, $local_dir, $port = 21)
    {
        $downloaded = false;

        try
        {
            if(is_dir($local_dir) && is_writable($local_dir))
            {
                if(false !== $cid = Ftp::login($server, $user, $password, $port))
                {
                    # If remote_dir do not ends with /
                    if(!HString::ends_with($remote_dir, '/'))
                    {
                        # Create fisrt level directory on local filesystem
                        $local_dir = rtrim($local_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($remote_dir);
                        mkdir($local_dir);
                    }
                    
                    # Remove trailing slash
                    $local_dir = rtrim($local_dir, DIRECTORY_SEPARATOR);

                    $downloaded = Ftp::download_all($cid, $remote_dir, $local_dir); 
                    ftp_close($cid); 
                }
            }
            else
            {
                throw new Exception("Local directory does not exist or is not writable", 1);
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::download_dir : " . $e->getMessage());
        }

        return $downloaded;
    }

    /**
     * Recursive function to download remote files
     *
     * @param ressource $cid
     * @param string $remote_dir
     * @param string $local_dir
     *
     * @return bool $download_all
     *
     */
    private static function download_all($cid, $remote_dir, $local_dir)
    {
        $download_all = false;

        try
        {
            if(Ftp::is_dir($remote_dir, $cid))
            {
                $files = ftp_nlist($cid, $remote_dir);
                if($files!==false)
                {
                    $to_download = 0;
                    $downloaded = 0;

                    # do this for each file in the remote directory 
                    foreach ($files as $file)
                    {
                        # To prevent an infinite loop 
                        if ($file != "." && $file != "..")
                        {
                            $to_download++;
                            # do the following if it is a directory 
                            if (Ftp::is_dir($file, $cid))// $remote_dir . DIRECTORY_SEPARATOR .
                            {                                
                                # Create directory on local filesystem
                                mkdir($local_dir . DIRECTORY_SEPARATOR . basename($file));
                                
                                # Recursive part 
                                if(Ftp::download_all($cid, $file, $local_dir . DIRECTORY_SEPARATOR . basename($file)))
                                {
                                    $downloaded++;
                                }
                            }
                            else
                            { 
                                # Download files 
                                if(ftp_get($cid, $local_dir . DIRECTORY_SEPARATOR . basename($file), $file, FTP_BINARY))
                                {
                                    $downloaded++;
                                }
                            } 
                        }
                    }

                    # Check all files and folders have been downloaded
                    if($to_download===$downloaded)
                    {
                        $download_all = true;
                    }
                }
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::download_all : " . $e->getMessage());
        }

        return $download_all;
    }


    /**
     * Rename a file on remote FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $old_file
     * @param string $new_file
     * @param int $port
     *
     * @return bool $renamed
     *
     */
    public static function rename($server, $user, $password, $old_file, $new_file, $port = 21)
    {
        $renamed = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                # Rename
                if(ftp_rename($cid, $old_file, $new_file))
                {
                    $renamed = true;
                }
                ftp_close($cid); 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::rename : " . $e->getMessage());
        }

        return $renamed;
    }

    /**
     * Create a directory on remote FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $directory
     * @param int $port
     *
     * @return bool $created
     *
     */
    public static function mkdir($server, $user, $password, $directory, $port = 21)
    {
        $created = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                if(!Ftp::is_dir($directory, $cid))
                {
                    # CREATE
                    if (ftp_mkdir($cid, $directory))
                    {
                        $created = true;
                    }
                }
                ftp_close($cid); 
            }
        }
        catch(Exception $e)
        {
            error_log("Ftp::mkdir : " . $e->getMessage());
        }

        return $created;
    }

    /**
     * Create a file on remote FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $remote_file
     * @param int $port
     * @param string $mode
     *
     * @return bool $created
     *
     */
    public static function touch($server, $user, $password, $remote_file, $content, $port = 21, $mode = FTP_BINARY)
    {
        $created = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                # Create temp file
                $local_file = tmpfile();
                fwrite($local_file, $content);
                fseek($local_file, 0);
                if(ftp_fput($cid, $remote_file, $local_file, $mode))
                {
                    $created = true;
                }
                fclose($local_file);
                ftp_close($cid); 
            } 
        }
        catch(Exception $e)
        {
            error_log("Ftp::touch : " . $e->getMessage());
        }

        return $created;
    }


    /**
     * Upload a file on FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $local_file
     * @param string $remote_file
     * @param int $port
     * @param string $mode (FTP_ASCII | FTP_BINARY)
     * @param string $timeout (default 1000 : 10 seconds)
     *
     * @return bool $uploaded
     *
     */
    public static function upload($server, $user, $password, $local_file, $remote_file, $port = 21, $mode = FTP_BINARY, $timeout = 1000)
    {
        $uploaded = false;

        try
        {
            if(false !== $cid = Ftp::login($server, $user, $password, $port))
            {
                if(ftp_put($cid, $remote_file, $local_file, $mode))
                {
                    $uploaded = true;
                }
                ftp_close($cid); 
            } 
        }
        catch(Exception $e)
        {
            error_log("Ftp::upload_file : " . $e->getMessage());
        }

        return $uploaded;
    }

    /**
     * List files on FTP server
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param string $path
     * @param int $port
     *
     * @return array $files Files listed in directory or false
     *
     */
    public static function scandir($server, $user, $password, $path, $port = 21)
    {
        $files = false;

        if(false !== $cid = Ftp::login($server, $user, $password, $port))
        {
            $files = ftp_nlist($cid, $path);
            ftp_close($cid); 
        } 

        return $files;
    }

    /**
     * Get default login FTP directory aka pwd
     *
     * @param string $server 
     * @param string $user
     * @param string $password
     * @param int $port
     *
     * @return string $dir Print Working Directory or false
     *
     */
    public static function pwd($server, $user, $password, $port = 21)
    {
        $dir = false;

        if(false !== $cid = Ftp::login($server, $user, $password, $port))
        {
            $dir = ftp_pwd($cid);
            ftp_close($cid); 
        } 

        return $dir;
    }

}