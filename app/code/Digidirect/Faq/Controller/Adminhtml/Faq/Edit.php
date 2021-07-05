<?php
namespace Digidirect\Faq\Controller\Adminhtml\Faq;

use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Digidirect\Faq\Model\FaqFactory;
use Digidirect\Faq\Model\ResourceModel\FaqRepository;
use Digidirect\Faq\Model\Registry\Constants;

/**
 * Class Edit
 * @package Digidirect\Faq\Controller\Adminhtml\Faq
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var  FaqFactory
     */
    protected $faqFactory;

    /**
     * @var FaqRepository
     */
    protected $faqRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param FaqFactory $faqFactory
     * @param FaqRepository $faqRepository
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        FaqFactory $faqFactory,
        FaqRepository $faqRepository
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->registry             = $registry;
        $this->faqFactory           = $faqFactory;
        $this->faqRepository        = $faqRepository;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id', 0);

        /**
         * @var $model \Digidirect\Faq\Model\Category
         */
        $model = $this->faqFactory->create();

        if ($id) {
            $model = $this->faqRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This FAQ item doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->registry->register(Constants::CURRENT_FAQ_ITEM, $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Digidirect_Faq::faq_faq')
            ->addBreadcrumb(__('Digidirect Faq Item'), __('Digidirect Faq Item'))
            ->addBreadcrumb(
                $id ? __('Edit FAQ Item') : __('Edit FAQ Item'),
                $id ? __('Edit FAQ Item') : __('New FAQ Item')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? 'Edit FAQ Item' : __('New FAQ Item'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Faq::faq');
    }
}
