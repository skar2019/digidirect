<?php
namespace Ewave\ProductAttachment\Model;

use Ewave\ProductAttachment\Api\Data\AttachmentInterface;
use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;

/**
 * @method getName()
 * @method getStatus()
 * @method getFilePath()
 *
 * Class Category
 * @package Ewave\Faq\Model
 */
class Attachment extends \Magento\Framework\Model\AbstractModel implements AttachmentInterface
{
    const STATUS_ACTIVE = 1;
    const ATTACHED_FLAG = 1;
    const NOT_ATTACHED_FLAG = 0;
    const DEFAULT_POSITION = 0;
    
    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Attachment constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Attachment $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ResourceModel\Attachment\CollectionFactory $resourceCollectionFactory
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param \Magento\Backend\Model\Session $session
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductAttachment\Model\ResourceModel\Attachment $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\ProductAttachment\Model\ResourceModel\Attachment\CollectionFactory $resourceCollectionFactory,
        AttachmentRepositoryInterface $attachmentRepository,
        \Magento\Backend\Model\Session $session,
        FileProcessor $fileProcessor
    ) {
        $resourceCollection = $resourceCollectionFactory->create();
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->session = $session;
        $this->fileProcessor = $fileProcessor;
        $this->attachmentRepository = $attachmentRepository;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @return array
     */
    public static function getAttachmentAttributes()
    {
        return ['name'];
    }
    
    /**
     * @return bool
     */
    public function saveAttachmentFile()
    {
        $attachmentData = $this->session->getProductAttachment();
        if (is_array($attachmentData) && !empty($attachmentData['file'])) {
            $this->removeFile();
            $filePath = $this->fileProcessor->moveFileFromTmp($this->getId(), $attachmentData['file']);
            $this->session->unsProductAttachment();
            $this->attachmentRepository->saveFilePath($this->getId(), $filePath);
        }
        return true;
    }

    /**
     * @return $this
     */
    public function removeFile()
    {
        $this->fileProcessor->unlink((int)$this->getId());
        return $this;
    }

    /**
     * @return string
     */
    public function getFileFormat()
    {
        return $this->fileProcessor->getFileExt($this->getFilePath());
    }

    /**
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileProcessor->getFileSize($this->getFilePath());
    }

    /**
     * @return string
     */
    public function getFileContent()
    {
        return $this->fileProcessor->readFile($this->getFilePath());
    }

    /**
     * @return string
     */
    public function getDownloadableFileName()
    {
        return $this->getName() . '.' . $this->getFileFormat();
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        return $this->getId() && $this->getStatus() == self::STATUS_ACTIVE;
    }

    /**
     * @return string
     */
    public function getWebUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
        . $this->fileProcessor->getMediaFilePath($this->getFilePath());
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return $this->fileProcessor->isFileExists($this->getFilePath());
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }
}
