<?php
namespace Digidirect\Navigation\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Digidirect\Navigation\Api\AbstractNavigationInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDeleteAbstract
 * @package Digidirect\Navigation\Controller\Adminhtml
 */
class MassDeleteAbstract extends AbstractMassAction
{
    /**
     * @var AbstractNavigationInterface
     */
    protected $repository;

    /**
     * MassDeleteAbstract constructor.
     * @param Context $context
     * @param Filter $filter
     * @param AbstractNavigationInterface $repository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        AbstractNavigationInterface $repository
    ) {
        parent::__construct($context, $filter);
        $this->repository = $repository;
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
        foreach ($collection->getAllIds() as $setId) {
            $this->repository->deleteById($setId);
            $recordsDeleted++;
        }

        if ($recordsDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordsDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
