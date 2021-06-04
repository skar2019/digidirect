<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\Field;

use Ewave\ProductCalculator\Controller\Adminhtml\Field;
use Ewave\ProductCalculator\Ui\DataProvider\Field\Form\DataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Ewave\ProductCalculator\Controller\Adminhtml\Field
 */
class Save extends Field
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Ewave\ProductCalculator\Model\FieldRepository $fieldRepository
     * @param \Ewave\ProductCalculator\Model\FieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Ewave\ProductCalculator\Model\FieldRepository $fieldRepository,
        \Ewave\ProductCalculator\Model\FieldFactory $fieldFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fieldRepository = $fieldRepository;
        $this->fieldFactory = $fieldFactory;
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
            $model = $this->fieldFactory->create();
            if ($id) {
                $model = $this->fieldRepository->getById($id);
            }
            $model->setData($data);
            try {
                $this->fieldRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the field.'));
                $this->dataPersistor->clear(DataProvider::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the field.'));
            }

            $this->dataPersistor->set(DataProvider::DATA_PERSISTOR_KEY, $data);
            return $editPageRedirect;
        }
        return $resultRedirect->setPath('*/*/');
    }
}
