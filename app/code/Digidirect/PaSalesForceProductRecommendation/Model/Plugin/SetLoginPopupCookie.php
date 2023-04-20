<?php


namespace Digidirect\PaSalesForceProductRecommendation\Model\Plugin;


use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Digidirect\PaSalesForceProductRecommendation\Helper\Data as PopupHelper;


/**
 * Class SetLoginPopupCookie
 * @package Digidirect\PaSalesForceProductRecommendation\Model\Plugin
 */
class SetLoginPopupCookie
{
    /**
     * @var CookieManagerInterface
     */
    private CookieManagerInterface $cookieManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $_customerRepository;

    /**
     * @var CookieMetadataFactory
     */
    private CookieMetadataFactory $cookieMetadataFactory;

    /**
     * @var PopupHelper
     */
    private PopupHelper $popupHelper;

    /**
     * SetLoginPopupCookie constructor.
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param PopupHelper $popupHelper
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        CustomerRepositoryInterface $customerRepository,
        PopupHelper $popupHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_customerRepository = $customerRepository;
        $this->popupHelper = $popupHelper;
    }

    /**
     * @param Session $customerSession
     * @param $result
     * @return mixed
     */
    public function afterSetCustomerDataAsLoggedIn(Session $customerSession, $result)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);

        try {
            $customer = $this->_customerRepository->getById($customerSession->getCustomerId());
            if($customer->getId()) {
                $this->cookieManager->setPublicCookie('can_show_login_popup', "1", $publicCookieMetadata);

                $this->cookieManager->setPublicCookie('show_popup_on_each_config', $this->popupHelper->getShowPopupOnEachLoginConfig(), $publicCookieMetadata);
                if($this->cookieManager->getCookie('displayed_login_popup') === "1" && !$this->popupHelper->getShowPopupOnEachLoginConfig()) {
                    $this->cookieManager->setPublicCookie('displayed_login_popup', "1", $publicCookieMetadata);
                } else {
                    $this->cookieManager->setPublicCookie('displayed_login_popup', "0", $publicCookieMetadata);
                }
            } else {
                $this->cookieManager->setPublicCookie('can_show_login_popup', "0", $publicCookieMetadata);
            }
        } catch (NoSuchEntityException $e) {
            // Here you can log the error if you want that no customer exist
            try {
                $this->cookieManager->setPublicCookie('can_show_login_popup', "0", $publicCookieMetadata);
            } catch (InputException $e) {
                // Here you can log input related error
            } catch (CookieSizeLimitReachedException $e) {
                // Here you can log Cookie's size limit related error
            } catch (FailureToSendException $e) {
                // Here you can log failure send related error
            }
        } catch (LocalizedException $e) {
            // Here you can log the error
            try {
                $this->cookieManager->setPublicCookie('can_show_login_popup', "0", $publicCookieMetadata);
            } catch (InputException $e) {
                // Here you can log input related error
            } catch (CookieSizeLimitReachedException $e) {
                // Here you can log Cookie's size limit related error
            } catch (FailureToSendException $e) {
                // Here you can log failure send related error
            }
        } catch (InputException $e) {
            // Here you can log input related error
        } catch (CookieSizeLimitReachedException $e) {
            // Here you can log Cookie's size limit related error
        } catch (FailureToSendException $e) {
            // Here you can log failure send related error
        }
        return $result;
    }
}