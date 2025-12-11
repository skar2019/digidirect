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

    public function __construct(Sftp $sftp,File $file, \Magento\Framework\Filesystem\DirectoryList $directoryList, ScopeConfigInterface $scopeConfig)
    {
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
    }

    public function sendFile()
    {
        $destinationPath = '/uploads/wiserdata.csv';
        $tempDestinationPath = '/uploads/wiserdata.csv.tmp'; // Critical for 38MB file
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/wiserdata.csv';

        // ... your config code ...

        try {
            $this->sftp->open($sftpConfig);

            // Takes 5+ minutes to upload to .tmp (their system ignores it)
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
                // Ignore
            }

            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
