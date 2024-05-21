<?php

namespace Digidirect\SftpDownload\Controller\Index;

use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Filesystem\Io\File;

class Pdf extends \Magento\Framework\App\Action\Action
{
    protected $sftp;

    protected $file;

    protected $directoryList;

    public function __construct(
        Sftp $sftp,
        File $file,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ){
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        return parent::__construct($context);
    }

    public function execute()
    {
        if(isset($_GET["file"])){

            $fileName = $_GET["file"];
            $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/'. $fileName;
            $targetFile = 'sftp://magentosftp@119.82.149.212:6999/MAGENTO/'.$fileName;

            $sftpConfig = [
                'host' => '119.82.149.212',
                'port' => '6999',
                'username' => 'magentosftp',
                'password' => 'CsUd!1G9#mdEui$z1zG#k94%xXk!0DZq'
            ];

            try {
                $this->sftp->open($sftpConfig);
                $this->sftp->cd('/MAGENTO/');
                
                //Fetching/Listing all the files.
                /*$sftp_server_files = $this->sftp->ls();
                foreach ($sftp_server_files as $file) {
                    $source = $file['text'];
                    echo $source . "\n";
                }*/
                
                $result = $this->sftp->read($fileName, $filePath);
                //$this->sftp->write($targetFile, $filePath);
                //$this->sftp->close();
                echo $fileName . ', ' . $filePath ."\n";
                if($result == true) {
                    echo 'File read from SFTP server';
                }
                else
                {
                    echo 'File not able to read from SFTP server';
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }

        } else {
            echo 'Invalid file!';
        }
    }
}
