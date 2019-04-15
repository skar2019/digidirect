<?php
namespace Ewave\Faq\Controller\Adminhtml\Category;

use Ewave\Faq\Api\AbstractFaqInterface;
use Ewave\Faq\Controller\Adminhtml\MassDeleteAbstract;
use Ewave\Faq\Api\CategoryRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Faq\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Ewave\Faq\Controller\Adminhtml\Category
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Ewave_Faq::faq_category_items_delete';

    /**
     * @var AbstractFaqInterface
     */
    protected $repository;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $categoryRepository);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Delete record using repository
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $recordsDeleted = 0;
        $restrictDeleted = 0;
        /** @var \Ewave\Faq\Model\Category $item */
        foreach ($collection as $item) {
            if ($item->canBeDeleted()) {
                $this->repository->deleteById($item->getId());
                $recordsDeleted++;
            } else {
                $restrictDeleted++;
            }
        }

        if ($recordsDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordsDeleted));
        }

        if ($restrictDeleted) {
            $this->messageManager->addWarningMessage(
                __(
                    'The category can\'t be deleted. Please unassign FAQs and try again.',
                    $restrictDeleted
                )
            );
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
