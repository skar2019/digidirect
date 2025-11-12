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
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/wiserdata.csv';

        $host = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/host');
        $compcode = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/port');
        $user = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/username');
        $token = $this->scopeConfig->getValue('wiser_settings_section/pronto_group/password');

        $sftpConfig = [
            'host' => 'sftp.360pi.com',
            'port' => '22',
            'username' => 'digidirect',
            'password' => '#uIHOZNk3&e9677c'
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
