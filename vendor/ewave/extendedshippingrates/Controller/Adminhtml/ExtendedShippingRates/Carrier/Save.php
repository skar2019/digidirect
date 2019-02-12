<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Carrier;

class Save extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Carrier
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Ewave\ExtendedShippingRates\Model\CarrierFactory $carrierFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ewave\ExtendedShippingRates\Api\CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Ewave\ExtendedShippingRates\Model\CarrierFactory $carrierFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Psr\Log\LoggerInterface $logger,
        \Ewave\ExtendedShippingRates\Api\CarrierRepositoryInterface $carrierRepository
    ) {

        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $dateFilter,
            $carrierFactory,
            $carrierRepository,
            $logger
        );
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Carrier save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('ewave_extendedshippingrates/*/');
        }

        try {
            /** @var $model \Ewave\ExtendedShippingRates\Model\Carrier */
            $model = $this->carrierFactory->create();
            $this->_eventManager->dispatch(
                'adminhtml_controller_ewave_extendedshippingrates_carrier_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue('carrier');
            $data['store_labels'] = $this->getRequest()->getPostValue('store_labels');
            $inputFilter = new \Zend_Filter_Input(
                ['created_at' => $this->dateFilter, 'updated_at' => $this->dateFilter],
                [],
                $data
            );
            $data = $inputFilter->getUnescaped();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model = $this->carrierRepository->getById($id);
            }

            $data = $this->prepareData($data);
            $validateResult = $model->validateData($this->dataObjectFactory->create(['data' => $data]));
            if ($validateResult !== true) {
                foreach ($validateResult as $errorMessage) {
                    $this->messageManager->addErrorMessage($errorMessage);
                }
                $this->_session->setPageData($data);
                $this->_redirect('ewave_extendedshippingrates/*/edit', ['id' => $model->getId()]);
                return;
            }

            $model->loadPost($data);
            $model->addData($data);

            $this->_session->setPageData($model->getData());

            $this->carrierRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the carrier.'));
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('ewave_extendedshippingrates/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('ewave_extendedshippingrates/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('ewave_extendedshippingrates/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('ewave_extendedshippingrates/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the carrier data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('ewave_extendedshippingrates/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (empty($data['carrier_id'])) {
            $data['carrier_id'] = null;
        }

        if (empty($data['carrier_code']) && !empty($data['title'])) {
            $code = mb_strtolower($data['title']);
            $code = preg_replace('/[^\da-z]/i', '', $code);
            $code = 'code' . $code;
            $data['carrier_code'] = $code;
        }

        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
