<?php
namespace Ewave\ProductAttachment\Controller\Download;

use Ewave\ProductAttachment\Model\Registry\Constants;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 * @package Ewave\ProductAttachment\Controller\Download
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ewave\ProductAttachment\Model\AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Ewave\ProductAttachment\Model\AttachmentRepository $attachmentRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductAttachment\Model\AttachmentRepository $attachmentRepository
    ) {
        $this->fileFactory = $fileFactory;
        $this->attachmentRepository = $attachmentRepository;
        $this->storeManager = $storeManager;
        $registry->register(Constants::CURRENT_STORE_ID, $this->storeManager->getStore()->getId());
        parent::__construct($context);
    }

    /**
     * @throws NotFoundException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $attachment = $this->attachmentRepository->getByIdWithAttributes((int)$id);
        if (!$attachment->isDownloadable()) {
            throw new NotFoundException(__('File not found.'));
        }
        $this->fileFactory->create(
            $attachment->getDownloadableFileName(),
            $attachment->getFileContent(),
            DirectoryList::MEDIA
        );
    }
}
