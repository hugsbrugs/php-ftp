<?php

# For PHP7
// declare(strict_types=1);

// namespace Hug\Tests\Ftp;

use PHPUnit\Framework\TestCase;

use Hug\Ftp\Ftp as Ftp;

/**
 *
 */
final class FtpTest extends TestCase
{    



    /* ************************************************* */
    /* ******************* Ftp::login ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanLogin()
    {
        $test = Ftp::login($server, $user, $password);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ******************* Ftp::test ******************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanTest()
    {
        $test = Ftp::test($server, $user, $password);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::is_dir ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsDir()
    {
        $test = Ftp::is_dir($dir, $cid);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::is_file ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsFile()
    {
        $test = Ftp::is_file($server, $user, $password, $remote_file);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::delete ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDelete()
    {
        $test = Ftp::delete($server, $user, $password, $remote_file);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::rmdir ******************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRmdir()
    {
        $test = Ftp::rmdir($server, $user, $password, $remote_path);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ***************** Ftp::clean_dir **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanCleanDir()
    {
        $test = Ftp::clean_dir($path, $cid);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* **************** Ftp::upload_dir **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanUploadDir()
    {
        $test = Ftp::upload_dir($server, $user, $password, $local_path, $remote_path);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* **************** Ftp::upload_all **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanUploadAll()
    {
        $test = Ftp::upload_all($cid, $local_dir, $remote_dir);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ***************** Ftp::download ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownload()
    {
        $test = Ftp::download($server, $user, $password, $remote_file, $local_file);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* *************** Ftp::download_dir *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownloadDir()
    {
        $test = Ftp::download_dir($server, $user, $password, $remote_dir, $local_dir);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* *************** Ftp::download_all *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownloadAll()
    {
        $test = Ftp::download_all($cid, $remote_dir, $local_dir);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::rename ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRename()
    {
        $test = Ftp::rename($server, $user, $password, $old_file, $new_file);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ******************* Ftp::mkdir ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanMkdir()
    {
        $test = Ftp::mkdir($server, $user, $password, $directory);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ******************* Ftp::touch ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanTouch()
    {
        $test = Ftp::touch($server, $user, $password, $remote_file, $content, $mode = FTP_BINARY);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::upload ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanUpload()
    {
        $test = Ftp::upload($server, $user, $password, $local_file, $remote_file = '', $mode = FTP_BINARY, $timeout = 1000);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** Ftp::scandir ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanScandir()
    {
        $test = Ftp::scandir($server, $user, $password, $path);
        $this->assertTrue($test);
    }


    /* ************************************************* */
    /* ******************** Ftp::pwd ******************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanPwd()
    {
        $test = Ftp::pwd($server, $user, $password);
        $this->assertTrue($test);
    }

}
