<?php
namespace Digidirect\CustomOptions\Controller\Adminhtml\Create;
class Index extends \Magento\Backend\App\Action
{
         protected $resultPageFactory = false;      
         protected $customOption;
         
         public function __construct(
                 \Magento\Backend\App\Action\Context $context,
                 \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                 \Digidirect\CustomOptions\Model\CustomOption $customOption
         ) {            
                 $this->customOption = $customOption;
                 $this->resultPageFactory = $resultPageFactory;
                 parent::__construct($context);
         } 
         public function execute()
         {
                 $this->customOption->saveCustomOption(43);
                 $resultPage = $this->resultPageFactory->create();
                 $resultPage->setActiveMenu('Magento_Catalog::catalog');
                 $resultPage->getConfig()->getTitle()->prepend(__('Customizable Options Created'));
                 return $resultPage;
         }
         protected function _isAllowed()
         {
                 return $this->_authorization->isAllowed('Magento_Catalog::catalog');
         }
}