<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Method;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ExtendedShippingRates\Model\ResourceModel\Method\CollectionFactory;
use Ewave\ExtendedShippingRates\Api\MethodRepositoryInterface;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Ewave\ExtendedShippingRates\Model\ResourceModel\Method\CollectionFactory
     */
    protected $methodCollectionFactory;

    /**
     * @var MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $methodCollectionFactory
     * @param MethodRepositoryInterface $methodRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $methodCollectionFactory,
        MethodRepositoryInterface $methodRepository
    ) {
        $this->filter = $filter;
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->methodRepository = $methodRepository;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->methodCollectionFactory->create()
        );
        $ids = $collection->getAllIds();
        $size = count($ids);
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method $method */
        foreach ($ids as $id) {
            $this->methodRepository->deleteById($id);
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $this->messageManager
            ->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $size)
            );
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ExtendedShippingRates::carrier');
    }
}
