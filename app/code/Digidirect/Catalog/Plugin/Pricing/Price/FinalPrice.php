<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\SaleableInterface;
use Psr\Log\LoggerInterface;

class FinalPrice
{
    private const DIGICLUB_GROUP_ID = 10;

    private const DISCOUNT_TIERS = [
        2 => [],
        5 => [],
        10 => [],
        15 => []
    ];

    private Session $customerSession;
    private LoggerInterface $logger;
    private UserContextInterface $userContext;
    private CustomerRepositoryInterface $customerRepository;
    private State $appState;

    public function __construct(
        Session $customerSession,
        LoggerInterface $logger,
        UserContextInterface $userContext,
        CustomerRepositoryInterface $customerRepository,
        State $appState
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->appState = $appState;
    }

    /**
     * Override final price with wiser_price if lower and apply DigiClub discounts
     *
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $subject
     * @param float $result
     * @return float
     */
    public function afterGetValue(
        \Magento\Catalog\Pricing\Price\FinalPrice $subject,
                                                  $result
    ) {
        try {
            $product = $subject->getProduct();

            // Validate product object
            if (!$product instanceof SaleableInterface) {
                $this->logger->warning('FinalPrice plugin: Invalid product object', [
                    'product_type' => is_object($product) ? get_class($product) : gettype($product)
                ]);
                return $result;
            }

            // Validate result is numeric
            if (!is_numeric($result)) {
                $this->logger->error('FinalPrice plugin: Non-numeric result received', [
                    'sku' => $product->getSku(),
                    'result' => $result,
                    'result_type' => gettype($result)
                ]);
                return $result;
            }

            $wiserPrice = $this->getWiserPrice($product);

            // If no valid wiser price or not lower than current price, return original
            if ($wiserPrice === null || $wiserPrice <= 0 || $wiserPrice >= $result) {
                return $result;
            }

            // Don't apply wiser pricing to DigiPrint products
            if ($this->isDigiPrintProduct($product)) {
                return $result;
            }

            // Apply DigiClub discount if customer is a member
            if ($this->isDigiClubMember()) {
                $discountedPrice = $this->applyDigiClubDiscount($wiserPrice, $product->getSku());

                // Validate discounted price is reasonable
                if ($discountedPrice > 0 && $discountedPrice < $wiserPrice) {
                    return $discountedPrice;
                } else {
                    $this->logger->warning('FinalPrice plugin: Invalid discounted price calculated', [
                        'sku' => $product->getSku(),
                        'wiser_price' => $wiserPrice,
                        'discounted_price' => $discountedPrice
                    ]);
                    return $wiserPrice;
                }
            }

            return $wiserPrice;

        } catch (\Throwable $e) {
            // Log the error but don't break pricing - return original price
            $this->logger->critical('FinalPrice plugin: Unexpected error in afterGetValue', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sku' => isset($product) ? $product->getSku() : 'unknown',
                'original_price' => $result
            ]);

            return $result;
        }
    }

    /**
     * Get wiser price from product with validation
     *
     * @param SaleableInterface $product
     * @return float|null
     */
    private function getWiserPrice(SaleableInterface $product): ?float
    {
        try {
            $wiserPrice = $product->getData('wiser_price');

            if ($wiserPrice === null || $wiserPrice === '') {
                return null;
            }

            if (!is_numeric($wiserPrice)) {
                $this->logger->warning('FinalPrice plugin: Non-numeric wiser_price', [
                    'sku' => $product->getSku(),
                    'wiser_price' => $wiserPrice,
                    'type' => gettype($wiserPrice)
                ]);
                return null;
            }

            return (float)$wiserPrice;

        } catch (\Exception $e) {
            $this->logger->error('FinalPrice plugin: Error getting wiser_price', [
                'sku' => $product->getSku(),
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if product is a DigiPrint product
     *
     * @param SaleableInterface $product
     * @return bool
     */
    private function isDigiPrintProduct(SaleableInterface $product): bool
    {
        try {
            return (bool)$product->getData('is_digiprint');
        } catch (\Exception $e) {
            $this->logger->error('FinalPrice plugin: Error checking is_digiprint', [
                'sku' => $product->getSku(),
                'exception' => $e->getMessage()
            ]);
            return false; // Default to applying pricing if we can't determine
        }
    }

    /**
     * Check if current customer is a DigiClub member
     * Handles both web session and API token authentication
     *
     * @return bool
     */
    private function isDigiClubMember(): bool
    {
        try {
            // Handle API requests (webapi_rest/webapi_soap area)
            $areaCode = $this->getAreaCode();

            if ($areaCode === 'webapi_rest' || $areaCode === 'webapi_soap') {
                return $this->isDigiClubMemberViaApi();
            }

            // Handle frontend/adminhtml requests via session
            return $this->isDigiClubMemberViaSession();

        } catch (\Throwable $e) {
            $this->logger->error('FinalPrice plugin: Error checking DigiClub membership', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false; // Fail safely - don't apply discount if we can't verify
        }
    }

    /**
     * Get current area code safely
     *
     * @return string|null
     */
    private function getAreaCode(): ?string
    {
        try {
            return $this->appState->getAreaCode();
        } catch (LocalizedException $e) {
            // Area code not set yet (e.g., during CLI operations)
            return null;
        }
    }

    /**
     * Check DigiClub membership via API authentication
     *
     * @return bool
     */
    private function isDigiClubMemberViaApi(): bool
    {
        try {
            $customerId = $this->userContext->getUserId();

            if (!$customerId) {
                return false;
            }

            $customer = $this->customerRepository->getById($customerId);
            $groupId = $customer->getGroupId();

            return $groupId == self::DIGICLUB_GROUP_ID;

        } catch (NoSuchEntityException $e) {
            $this->logger->warning('FinalPrice plugin: Customer not found in API context', [
                'customer_id' => $this->userContext->getUserId()
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('FinalPrice plugin: Error checking DigiClub via API', [
                'exception' => $e->getMessage(),
                'customer_id' => $this->userContext->getUserId() ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Check DigiClub membership via customer session
     *
     * @return bool
     */
    private function isDigiClubMemberViaSession(): bool
    {
        try {
            if (!$this->customerSession->isLoggedIn()) {
                return false;
            }

            $customer = $this->customerSession->getCustomer();

            if (!$customer || !$customer->getId()) {
                $this->logger->warning('FinalPrice plugin: Invalid customer in session');
                return false;
            }

            $groupId = $customer->getGroupId();

            return $groupId == self::DIGICLUB_GROUP_ID;

        } catch (\Exception $e) {
            $this->logger->error('FinalPrice plugin: Error checking DigiClub via session', [
                'exception' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Apply tiered discount based on SKU
     *
     * @param float $price
     * @param string $sku
     * @return float
     */
    private function applyDigiClubDiscount(float $price, string $sku): float
    {
        try {
            // Validate inputs
            if ($price <= 0) {
                $this->logger->warning('FinalPrice plugin: Invalid price for discount', [
                    'sku' => $sku,
                    'price' => $price
                ]);
                return $price;
            }

            if (empty($sku)) {
                $this->logger->warning('FinalPrice plugin: Empty SKU for discount calculation');
                return $price;
            }

            foreach (self::DISCOUNT_TIERS as $percentage => $skuList) {
                if (in_array($sku, $skuList, true)) {
                    // Validate percentage
                    if ($percentage < 0 || $percentage > 100) {
                        $this->logger->error('FinalPrice plugin: Invalid discount percentage', [
                            'sku' => $sku,
                            'percentage' => $percentage
                        ]);
                        return $price;
                    }

                    $discountMultiplier = 1 - ($percentage / 100);
                    $discountedPrice = $price * $discountMultiplier;

                    // Validate result
                    if ($discountedPrice <= 0 || $discountedPrice > $price) {
                        $this->logger->error('FinalPrice plugin: Invalid discount calculation', [
                            'sku' => $sku,
                            'original_price' => $price,
                            'percentage' => $percentage,
                            'discounted_price' => $discountedPrice
                        ]);
                        return $price;
                    }

                    // Optional: Log successful discount application
                    $this->logger->info('FinalPrice plugin: DigiClub discount applied', [
                        'sku' => $sku,
                        'percentage' => $percentage,
                        'original_price' => $price,
                        'discounted_price' => $discountedPrice
                    ]);

                    return $discountedPrice;
                }
            }

            // No discount tier matched
            return $price;

        } catch (\Throwable $e) {
            $this->logger->critical('FinalPrice plugin: Error applying discount', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sku' => $sku,
                'price' => $price
            ]);

            // Return original price on error
            return $price;
        }
    }
}
