<?php
namespace Digidirect\Faq\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class NewAction
 * @package Digidirect\Faq\Controller\Adminhtml\Category
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Faq::category');
    }
}
