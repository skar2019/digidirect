<?php

namespace Ewave\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ewave\Faq\Api\FaqRepositoryInterface;
use Ewave\Faq\Model\Email\Customer;

/**
 * Class Delete
 * @package Ewave\Faq\Controller\Adminhtml\Faq
 */
class Answer extends Action
{
    /**
     * @var FaqRepositoryInterface
     */
    protected $faqRepository;

    /**
     * @var Customer
     */
    protected $customerEmailModel;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param FaqRepositoryInterface $faqRepository
     * @param Customer $customer
     */
    public function __construct(
        Action\Context $context,
        FaqRepositoryInterface $faqRepository,
        Customer $customer
    ) {
        parent::__construct($context);
        $this->faqRepository = $faqRepository;
        $this->customerEmailModel = $customer;
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
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->customerEmailModel->sendEmail($this->faqRepository->getById($id));
                $this->messageManager->addSuccessMessage(__('Email has been sent to customer'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Faq::faq_faq_items_answer');
    }
}
