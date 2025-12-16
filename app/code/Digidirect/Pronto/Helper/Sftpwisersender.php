<?php

namespace Digidirect\Pronto\Helper;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Sftpwisersender extends AbstractHelper
{
    protected $sftp;
    protected $file;
    protected $directoryList;
    protected $scopeConfig;
    protected $logger;

    public function __construct(
        Sftp $sftp,
        File $file,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
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
            $this->logger->info('SFTP connection opened');

            // Upload to .tmp file first
            $writeResult = $this->sftp->write($tempDestinationPath, $this->file->read($filePath));
            $this->logger->info('Temp file write result: ' . ($writeResult ? 'SUCCESS' : 'FAILED'));

            if (!$writeResult) {
                throw new \Exception('Failed to write temporary file');
            }

            // Try to rename
            $this->logger->info('Attempting to rename from ' . $tempDestinationPath . ' to ' . $destinationPath);
            $mvResult = $this->sftp->mv($tempDestinationPath, $destinationPath);
            $this->logger->info('Rename result: ' . ($mvResult ? 'SUCCESS' : 'FAILED'));

            if (!$mvResult) {
                throw new \Exception('Failed to rename file from .tmp to final destination');
            }

            $this->sftp->close();
            $this->logger->info('File uploaded and renamed successfully');
            echo "File uploaded successfully";
            return true;
        } catch (\Exception $e) {
            $this->logger->error('SFTP Error: ' . $e->getMessage());

            // Cleanup and error handling
            try {
                $this->sftp->rm($tempDestinationPath);
                $this->logger->info('Cleaned up temp file');
                $this->sftp->close();
            } catch (\Exception $cleanupException) {
                $this->logger->error('Cleanup error: ' . $cleanupException->getMessage());
            }

            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
