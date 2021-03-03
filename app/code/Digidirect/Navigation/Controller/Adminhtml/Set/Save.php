<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Set;

use Digidirect\Navigation\Api\SetRepositoryInterface;
use Digidirect\Navigation\Model\SetFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;

/**
 * Class Save
 * @package Digidirect\Navigation\Controller\Adminhtml\Set
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Digidirect\Navigation\Model\SetFactory
     */
    protected $setFactory;

    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param \Digidirect\Navigation\Model\SetFactory $setFactory
     * @param SetRepositoryInterface $setRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        SetFactory $setFactory,
        SetRepositoryInterface $setRepository
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->setRepository = $setRepository;
        $this->setFactory = $setFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            /** @var \Digidirect\Navigation\Model\Set $model */
            $model = $this->_initSet($data);

            try {

                $this->_eventManager->dispatch(
                    'menu_set_prepare_save',
                    ['set' => $model, 'request' => $this->getRequest()]
                );

                $this->setRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved navigation set.'));
                $this->dataPersistor->clear(\Digidirect\Navigation\Helper\Data::DIGIDIRECT_NAVIGATION_CURRENT_SET_REGISTRY_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['set_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the navigation set.'));
            }

            $this->dataPersistor->set(\Digidirect\Navigation\Helper\Data::DIGIDIRECT_NAVIGATION_CURRENT_SET_REGISTRY_KEY, $data);
            return $resultRedirect->setPath('*/*/edit', ['set_id' => $this->getRequest()->getParam('set_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Initialize set
     *
     * @param [] $data
     * @return \Digidirect\Navigation\Api\Data\SetInterface
     * @throws LocalizedException
     */
    protected function _initSet($data)
    {
        /**
         * @var $model \Digidirect\Navigation\Model\Set
         */
        $id = $this->getRequest()->getParam('set_id');
        if ($id) {
            $model = $this->setRepository->getById($id);
        } else {
            $data['set_id'] = null;
            $model = $this->setFactory->create();
        }

        if (!$model->getId() && $id) {
            throw new LocalizedException(__('This navigation set no longer exists.'));
        }
        $model->setData($data);
        return $model;
    }

    /**
     * Check if Is allowed to update/save
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Navigation::navigation_menu_sets_save');
    }
}
