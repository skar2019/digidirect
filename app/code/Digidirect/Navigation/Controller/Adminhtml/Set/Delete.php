<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Set;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Digidirect\Navigation\Api\SetRepositoryInterface;

class Delete extends Action
{
    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param SetRepositoryInterface $setRepository
     */
    public function __construct(
        Action\Context $context,
        SetRepositoryInterface $setRepository
    ) {
        parent::__construct($context);
        $this->setRepository = $setRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('set_id');
        if ($id) {
            try {
                $this->setRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the navigation set.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['set' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a set to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Navigation::navigation_menu_sets_delete');
    }
}
