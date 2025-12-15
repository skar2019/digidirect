<?php

namespace Digidirect\Pronto\Helper;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Sftpwisersender extends AbstractHelper
{
    protected $sftp;
    protected $file;
    protected $directoryList;
    protected $scopeConfig;

    public function __construct(Sftp $sftp, File $file, \Magento\Framework\Filesystem\DirectoryList $directoryList, ScopeConfigInterface $scopeConfig)
    {
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
    }

    public function sendFile()
    {
        $destinationPath = '/uploads/wiserdata.csv';
        $tempDestinationPath = '/uploads/wiserdata.csv.tmp';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/wiserdata.csv';

        $host = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/host');
        $port = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/port');
        $user = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/username');
        $password = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/password');

        $sftpConfig = [
            'host' => $host,
            'port' => $port,
            'username' => $user,
            'password' => $password
        ];

        try {
            $this->sftp->open($sftpConfig);

            // Upload to .tmp file first (takes 5+ minutes but their system ignores it)
            $this->sftp->write($tempDestinationPath, $this->file->read($filePath));

            // Instant rename - file appears complete immediately
            $this->sftp->mv($tempDestinationPath, $destinationPath);

            $this->sftp->close();
            echo "File uploaded successfully";
            return true;
        } catch (\Exception $e) {
            // Cleanup and error handling
            try {
                $this->sftp->rm($tempDestinationPath);
                $this->sftp->close();
            } catch (\Exception $cleanupException) {
                // Ignore cleanup errors
            }

            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
