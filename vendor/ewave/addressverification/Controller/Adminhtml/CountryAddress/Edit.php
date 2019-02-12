<?php
namespace Ewave\AddressVerification\Controller\Adminhtml\CountryAddress;

use Ewave\AddressVerification\Api\Data\CountryAddressAttributeInterface;
use Ewave\AddressVerification\Helper\Aupost;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ewave\AddressVerification\Model\CountryAddressAttributeFactory;
use Ewave\AddressVerification\Model\CountryAddressAttributeRepository;

/**
 * Class Edit
 * @package Ewave\AddressVerification\Controller\Adminhtml\CountryAddress
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
     * @var CountryAddressAttributeFactory
     */
    protected $countryAddressAttributeFactory;

    /**
     * @var CountryAddressAttributeRepository
     */
    protected $countryAddressAttributeRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param CountryAddressAttributeFactory $countryAddressAttributeFactory
     * @param CountryAddressAttributeRepository $countryAddressAttributeRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CountryAddressAttributeFactory $countryAddressAttributeFactory,
        CountryAddressAttributeRepository $countryAddressAttributeRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->countryAddressAttributeFactory = $countryAddressAttributeFactory;
        $this->countryAddressAttributeRepository = $countryAddressAttributeRepository;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        /**
         * @var $model \Ewave\AddressVerification\Model\CountryAddressAttribute
         */
        $model = $this->countryAddressAttributeFactory->create();
        if ($id) {
            $model = $this->countryAddressAttributeRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->registry->register(CountryAddressAttributeInterface::CURRENT_ITEM, $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_AddressVerification::address_verification')
            ->addBreadcrumb(__('Ewave Item'), __('Ewave Item'))
            ->addBreadcrumb(
                $id ? __('Edit Item') : __('New Item'),
                $id ? __('Edit Item') : __('New Item')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Country Address Attributes Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? 'Edit Item' : __('New Item'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_AddressVerification::address_verification');
    }
}
