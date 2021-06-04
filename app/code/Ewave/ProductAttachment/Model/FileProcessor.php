<?php
namespace Ewave\ProductAttachment\Model;

use Ewave\ProductAttachment\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class FileProcessor
 * @package Ewave\ProductAttachment\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileProcessor
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Data
     */
    protected $attachmentHelper;

    /**
     * Media Directory object (writable).
     *
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var File
     */
    protected $fileSystemDriver;
    
    /**
     * @var string
     */
    const FILE_DIR = 'productattachments';

    /**
     * FileProcessor constructor.
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param File $fileSystemDriver
     * @param Data $attachmentHelper
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $fileSystemDriver,
        Data $attachmentHelper
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->attachmentHelper = $attachmentHelper;
        $this->fileSystemDriver = $fileSystemDriver;
    }

    /**
     * Save file to temp media directory
     *
     * @param  string $fileId
     * @return array
     * @throws LocalizedException
     */
    public function saveToTmp($fileId)
    {
        try {
            $result = $this->save($fileId, $this->getAbsoluteTmpMediaPath());
            $result['url'] = $this->getTmpMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * @param int $entityId
     * @param string $fileName
     * @return string
     * @throws LocalizedException
     */
    public function moveFileFromTmp($entityId, $fileName)
    {
        $baseTmpImagePath = $this->getTmpAttachmentPath() . DIRECTORY_SEPARATOR . $fileName;
        $baseImagePath = $this->prepareAttachmentMediaDir($entityId) . DIRECTORY_SEPARATOR . $fileName;
        try {
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        
        return $this->getFolderHash($entityId) . '/' . $fileName;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function unlink($filePath)
    {
        $file = self::FILE_DIR . DIRECTORY_SEPARATOR .  $this->getFolderHash($filePath);
        return $this->mediaDirectory->delete($file);
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        $allowedExtensions = $this->attachmentHelper->getAllowedExtensions();
        if (!empty($allowedExtensions)) {
            return explode(',', $allowedExtensions);
        }
        return [];
    }

    /**
     * @param string $file
     * @return float
     */
    public function getFileSize($file)
    {
        return $this->mediaDirectory->stat($this->getMediaFilePath($file))['size'];
    }

    /**
     * @param string $file
     * @return string
     */
    public function readFile($file)
    {
        return $this->mediaDirectory->readFile($this->getMediaFilePath($file));
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isFileExists($file)
    {
        return $this->mediaDirectory->isExist($this->getMediaFilePath($file));
    }
    
    /**
     * @param string $file
     * @return string
     */
    public function getMediaFilePath($file)
    {
        return self::FILE_DIR . DIRECTORY_SEPARATOR . $file;
    }
    
    /**
     * @param string $file
     * @return string
     */
    public function getFileExt($file)
    {
        $fileInfo = $this->fileSystemDriver->getPathInfo($file);
        return $fileInfo['extension'];
    }
    
    /**
     * @param int $entityId
     * @return string
     */
    protected function prepareAttachmentMediaDir($entityId)
    {
        return self::FILE_DIR . DIRECTORY_SEPARATOR . $this->getFolderHash($entityId);
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    protected function getTmpMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
        . $this->getTmpAttachmentPath() . '/' . $this->prepareFile($file);
    }

    /**
     * Prepare file
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Retrieve absolute temp media path
     *
     * @return string
     */
    protected function getAbsoluteTmpMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath($this->getTmpAttachmentPath());
    }

    /**
     * @return string
     */
    protected function getTmpAttachmentPath()
    {
        return 'tmp/' . self::FILE_DIR;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getAbsoluteFilePath($file)
    {
        return $this->mediaDirectory->getAbsolutePath(self::FILE_DIR) . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Save image
     *
     * @param string $fileId
     * @param string $destination
     * @return array
     * @throws LocalizedException
     */
    protected function save($fileId, $destination)
    {
        $result = ['file' => '', 'size' => ''];
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        return array_intersect_key($uploader->save($destination), $result);
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getFolderHash($id)
    {
        return ($id % 10) . DIRECTORY_SEPARATOR . $id;
    }
}
