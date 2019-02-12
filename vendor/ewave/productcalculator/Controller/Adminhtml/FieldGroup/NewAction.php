<?php
namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup;

use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class NewAction
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
 */
class NewAction extends \Ewave\ProductCalculator\Controller\Adminhtml\FieldGroup
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * New action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
