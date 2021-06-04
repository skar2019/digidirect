<?php
namespace Ewave\MyStoreWidget\Controller\MyStoreWidget;

use Ewave\MyStoreWidget\Api\MyStoreRepositoryInterface;
use Ewave\MyStoreWidget\Model\MyStoreFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class Index
 * @package Ewave\MyStoreWidget\Controller\MyStoreWidget
 */
class Index extends Action
{
    /**
     * @var \Ewave\MyStoreWidget\Model\MyStoreRepository
     */
    protected $myStoreRepository;

    /**
     * @var \Ewave\MyStoreWidget\Model\MyStoreFactory
     */
    protected $myStoreFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param MyStoreRepositoryInterface $myStoreRepository
     * @param \Ewave\MyStoreWidget\Model\MyStoreFactory $myStoreFactory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        MyStoreRepositoryInterface $myStoreRepository,
        MyStoreFactory $myStoreFactory,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->myStoreRepository = $myStoreRepository;
        $this->myStoreFactory = $myStoreFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        return $this->_redirect('/');
    }
}
