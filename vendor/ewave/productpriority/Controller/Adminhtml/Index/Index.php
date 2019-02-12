<?php
namespace Ewave\ProductPriority\Controller\Adminhtml\Index;

/**
 * Class Index
 * @package Ewave\ProductPriority\Controller\Priority
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Ewave\ProductPriority\Model\PriorityFactory
     */
    protected $_priorityFactory;

    /**
     * @var \Ewave\ProductPriority\Helper\Config
     */
    protected $_configHelper;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\ProductPriority\Model\PriorityFactory $priorityFactory
     * @param \Ewave\ProductPriority\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductPriority\Model\PriorityFactory $priorityFactory,
        \Ewave\ProductPriority\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
        $this->_priorityFactory = $priorityFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            if ($this->_configHelper->isEnabled()) {
                $sortInstance = $this->_priorityFactory->create();
                $sortInstance->calculate();
                $this->messageManager->addSuccessMessage(__('Re-calculate success'));
            } else {
                $this->messageManager->addErrorMessage(__('Module is disabled'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_redirect($this->_redirect->getRedirectUrl('admin/system/config/'));
    }
}
