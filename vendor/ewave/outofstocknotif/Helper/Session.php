<?php
namespace Ewave\OutOfStockNotif\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Session
 * @package Ewave\OutOfStockNotif\Helper
 */
class Session extends AbstractHelper
{
    /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * Session constructor.
     *
     * @param Context $context
     * @param SessionFactory $customerSession
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        $session = $this->getSession();
        if ($session->isLoggedIn()) {
            return $session->getCustomer()->getEmail();
        }

        return '';
    }

    /**
     * @return mixed
     */
    protected function getSession()
    {
        return $this->customerSession->create();
    }
}
