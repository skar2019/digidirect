<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

use Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;
use Ewave\ProductCalculator\Ui\DataProvider\FieldGroupCategory\Form\DataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
 */
class Save extends FieldGroupCategory
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository
     */
    protected $fieldGroupCategoryRepository;

    /**
     * @var \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory
     */
    protected $fieldGroupCategoryFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository $fieldGroupCategoryRepository
     * @param \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory $fieldGroupCategoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Ewave\ProductCalculator\Model\FieldGroupCategoryRepository $fieldGroupCategoryRepository,
        \Ewave\ProductCalculator\Model\FieldGroupCategoryFactory $fieldGroupCategoryFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fieldGroupCategoryRepository = $fieldGroupCategoryRepository;
        $this->fieldGroupCategoryFactory = $fieldGroupCategoryFactory;
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
            $model = $this->fieldGroupCategoryFactory->create();
            if ($id) {
                $model = $this->fieldGroupCategoryRepository->getById($id);
            }
            $model->setData($data);
            try {
                $this->fieldGroupCategoryRepository->save($model);
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
