<?php

namespace Digidirect\Pronto\Helper;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class SftpMWaveSender extends AbstractHelper
{
    protected $sftp;
    protected $file;
    protected $directoryList;
    protected $scopeConfig;

    public function __construct(Sftp $sftp,File $file, \Magento\Framework\Filesystem\DirectoryList $directoryList, ScopeConfigInterface $scopeConfig)
    {
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
    }

    public function sendFile()
    {

        $destinationPath = '/uploads/digidirect.csv';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/digidirect.csv';
        $hostname = $this->scopeConfig->getValue('mwave_sftp/hostname');
        $username = $this->scopeConfig->getValue('mwave_sftp/username');
        $password = $this->scopeConfig->getValue('mwave_sftp/password');;

        $sftpConfig = [
            'host' => $hostname,
            'port' => '22',
            'username' => $username,
            'password' => $password
        ];

        try {
            $this->sftp->open($sftpConfig);
            $this->sftp->write($destinationPath, $this->file->read($filePath));
            $this->sftp->close();
            echo "send it";
            return true;
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the file transfer
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
