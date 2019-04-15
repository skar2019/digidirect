<?php
namespace Ewave\Faq\Controller\Adminhtml\Faq;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class NewAction
 * @package Ewave\Faq\Controller\Adminhtml\Faq
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
        return $this->_authorization->isAllowed('Ewave_Faq::faq');
    }
}
