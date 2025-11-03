<?php
namespace Digidirect\Customer\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Model\Wishlist;
use Magento\Framework\HTTP\Header;

class Customer implements ArgumentInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Session
     */
    protected $wishlist;

    /**
     * @var Header
     */
    protected $httpHeader;


    /**
     * Constructor
     *
     * @param Session $customerSession
     * @param Wishlist $wishlist
     */
    public function __construct(
        Session $customerSession,
        Wishlist $wishlist,
        Header $httpHeader
    ) {
        $this->customerSession = $customerSession;
        $this->wishlist = $wishlist;
        $this->httpHeader = $httpHeader;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get customer account URL
     *
     * @return string
     */
    public function getAccountUrl(): string
    {
        return '/customer/account';
    }

    /**
     * Get customer login URL
     *
     * @return string
     */
    public function getLoginUrl(): string
    {
        return '/customer/account/login';
    }

    /**
     * Get wishlist items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        $customerWishlist = $this->wishlist->loadByCustomerId($this->customerSession->getCustomerId(), true);
        return $customerWishlist->getItemsCount();
    }

    /**
     * Get customer first name
     *
     * @return string
     */
    public function getFirstName()
    {
        if ($this->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getFirstname();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getShowLoginOverlay() {
        return $this->customerSession->getShowLoginOverlay();
    }

    /**
     * @return mixed
     */
    public function unsetShowLoginOverlay() {
        return $this->customerSession->unsShowLoginOverlay();
    }

    /**
     * @return false|int
     */
    public function isMobile()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();

        $isMobile = preg_match('/Mobile|Android|iP(hone|od|ad)|IEMobile|BlackBerry|Opera Mini/i', $userAgent);
        return $isMobile;
    }
}
