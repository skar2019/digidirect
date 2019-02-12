<?php
namespace Ewave\AbstractEntity\Controller\Adminhtml\AbstractEntity;

use Ewave\AbstractEntity\Controller\Adminhtml\AbstractEntity as AbstractEntityController;
use Ewave\AbstractEntity\Model\AbstractEntity;
use Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AbstractEntity\Model\Registry\Constants;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Config;

class Save extends AbstractEntityController
{
    const ADMIN_RESOURCE_PREFIX = 'Ewave_AbstractEntity::abstractentity_record_save_';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var AbstractEntityRepositoryInterface
     */
    protected $abstractEntityRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param AbstractEntityRepositoryInterface $abstractEntityRepository
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param Config $_eavConfig
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        AbstractEntityRepositoryInterface $abstractEntityRepository,
        AttributeSetRepositoryInterface $attributeSetRepository,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        Config $_eavConfig
    ) {
        $this->abstractEntityRepository = $abstractEntityRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dateFilter = $dateFilter;
        $this->_eavConfig = $_eavConfig;
        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $resultForwardFactory,
            $abstractEntityRepository,
            $attributeSetRepository
        );
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
        $data = $this->getRequest()->getPostValue();
        $setId = (int)$this->getRequest()->getParam(AbstractEntityInterface::ATTRIBUTE_SET_ID);

        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');
            $storeId = (int)$this->getRequest()->getParam('store_id');

            try {
                if ($id) {
                    $model = $this->abstractEntityRepository->getById($id);
                } else {
                    $model = $this->_objectManager->create(AbstractEntity::class);
                }

                $data = $this->_filterPostData($data);
                $model->setData($data);

                if (isset($data['use_default']) && !empty($data['use_default'])) {
                    foreach ($data['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $model->setData($attributeCode, null);
                        }
                    }
                }

                $this->abstractEntityRepository->save($model);
                $this->_eventManager->dispatch('adminhtml_ewave_abstractentity_save_after', [
                    'entity' => $model
                ]);

                $this->messageManager->addSuccessMessage(__('You saved the record.'));
                $this->dataPersistor->clear(Constants::CURRENT_ABSTRACT_ENTITY);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'id' => $model->getId(),
                        'store' => $storeId,
                        AbstractEntityInterface::ATTRIBUTE_SET_ID => $setId
                    ]);
                }
                return $resultRedirect->setPath('*/*/', [AbstractEntityInterface::ATTRIBUTE_SET_ID => $setId]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
            }

            $this->dataPersistor->set(Constants::CURRENT_ABSTRACT_ENTITY, $data);
            return $resultRedirect->setPath('*/*/edit', [
                'id' => $id,
                'store' => $storeId,
                AbstractEntityInterface::ATTRIBUTE_SET_ID => $setId
            ]);
        }
        return $resultRedirect->setPath('*/*/', [AbstractEntityInterface::ATTRIBUTE_SET_ID => $setId]);
    }

    /**
     * Datetime data preprocessing
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    protected function _filterPostData($data)
    {
        if (!empty($data['attribute_set_id'])) {
            $entityType = $this->_eavConfig->getEntityType(AbstractEntity::ENTITY_TYPE);
            $attributes = $entityType->getAttributeCollection($data['attribute_set_id'])->getItems();
            $dateFieldFilters = [];
            foreach ($attributes as $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    $dateFieldFilters[$attribute->getAttributeCode()] = $this->dateFilter;
                }
            }

            // make sure the date is converted to internal format
            $inputFilter = new \Zend_Filter_Input(
                $dateFieldFilters,
                [],
                $data
            );

            try {
                $data = $inputFilter->getUnescaped();
            } catch (\Exception $e) {
                throw new LocalizedException(__('Post Data Validation Error: %1', $e->getMessage()));
            }
        }
        return $data;
    }
}
