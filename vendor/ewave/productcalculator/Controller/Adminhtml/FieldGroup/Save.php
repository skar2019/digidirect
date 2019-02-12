<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;
use Ewave\ProductCalculator\Ui\DataProvider\FieldGroup\Form\DataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class Save extends FieldGroup
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupRepository
     */
    protected $fieldGroupRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupFactory
     */
    protected $fieldGroupFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Ewave\ProductCalculator\Model\FieldGroupRepository $fieldGroupRepository
     * @param \Ewave\ProductCalculator\Model\FieldGroupFactory $fieldGroupFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Ewave\ProductCalculator\Model\FieldGroupRepository $fieldGroupRepository,
        \Ewave\ProductCalculator\Model\FieldGroupFactory $fieldGroupFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fieldGroupRepository = $fieldGroupRepository;
        $this->fieldGroupFactory = $fieldGroupFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $editPageRedirect = $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->fieldGroupFactory->create();
            if ($id) {
                $model = $this->fieldGroupRepository->getById($id);
            }
            $model->setData($data);
            try {
                $this->fieldGroupRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved field group.'));
                $this->dataPersistor->clear(DataProvider::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving field group.'));
            }

            $this->dataPersistor->set(DataProvider::DATA_PERSISTOR_KEY, $data);
            return $editPageRedirect;
        }
        return $resultRedirect->setPath('*/*/');
    }
}
