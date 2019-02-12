<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Carrier;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\ExtendedShippingRates\Model\ResourceModel\Carrier\CollectionFactory;
use Ewave\ExtendedShippingRates\Model\CarrierFactory;
use Ewave\ExtendedShippingRates\Api\CarrierRepositoryInterface;
use Ewave\ExtendedShippingRates\Model\CarrierRepository;

class MassChangeStatus extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var CollectionFactory
     */
    protected $carrierCollectionFactory;

    /**
     * @var CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @var CarrierRepository
     */
    protected $carrierRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $carrierCollectionFactory
     * @param CarrierFactory $carrierFactory
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $carrierCollectionFactory,
        CarrierFactory $carrierFactory,
        CarrierRepositoryInterface $carrierRepository
    ) {
        parent::__construct($context);
        $this->carrierCollectionFactory = $carrierCollectionFactory;
        $this->filter = $filter;
        $this->carrierFactory = $carrierFactory;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Update carriers's is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->carrierCollectionFactory->create());
            $updatedCarriersCount = 0;
            foreach ($collection->getAllIds() as $carrierId) {
                $carrier = $this->carrierRepository->getById($carrierId);
                $carrier->setData('active', $this->getRequest()->getParam('active'));
                $this->carrierRepository->save($carrier);
                $updatedCarriersCount++;
            }

            if ($updatedCarriersCount) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $updatedCarriersCount));
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('ewave_extendedshippingrates/extendedshippingrates_carrier/index');

            return $resultRedirect;
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
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
