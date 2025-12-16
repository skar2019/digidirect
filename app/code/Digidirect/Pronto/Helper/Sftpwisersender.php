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
            $this->sftp->write($destinationPath, $this->file->read($filePath));
            $this->sftp->close();
            $this->logger->info('Wiser SFTP: File uploaded successfully to ' . $destinationPath);
            echo "send it";
            return true;
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the file transfer
            $this->logger->error('Wiser SFTP Error: ' . $e->getMessage(), [
                'destination' => $destinationPath,
                'source' => $filePath,
                'host' => $host
            ]);
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
