<?php
namespace Digidirect\Faq\Controller\Adminhtml\Faq;

use Digidirect\Faq\Api\FaqRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Digidirect\Faq\Model\ResourceModel\Faq\CollectionFactory;

/**
 * Class MassStatus
 * @package Digidirect\Faq\Controller\Adminhtml\Category
 */
class MassStatus extends \Digidirect\Faq\Controller\Adminhtml\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Digidirect_Faq::faq_category_items_save';

    /**
     * @var FaqRepositoryInterface
     */
    protected $faqRepository;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FaqRepositoryInterface $faqRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FaqRepositoryInterface $faqRepository
    ) {
        parent::__construct($context, $filter);
        $this->faqRepository = $faqRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Process mass status change
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        $recordsUpdated = $this->faqRepository->updateStatus($collection->getAllIds(), (int)$status);

        if ($recordsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $recordsUpdated));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
