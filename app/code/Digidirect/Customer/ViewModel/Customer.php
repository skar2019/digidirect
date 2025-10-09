<?php
namespace Digidirect\Customer\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Customer implements ArgumentInterface
{
    protected $customerSession;

    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getAccountUrl(): string
    {
        return '/customer/account';
    }

    public function getLoginUrl(): string
    {
        return '/customer/account/login';
    }
}
