<?php
namespace Ewave\OutOfStockNotif\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\ProductAlert\Helper\Data as ProductAlertHelper;
use Magento\Store\Model\StoreManagerInterface;
use Ewave\OutOfStockNotif\Model\StockResolver;

/**
 * Class Data
 *
 * @package Ewave\OutOfStockNotif\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_IS_ENABLED_FOR_GUEST = 'ewave_outofstocknotif/stock_subscription/enabled_for_guest';
    const XML_PATH_IS_ENABLED_FOR_BACKORDER = 'ewave_outofstocknotif/stock_subscription/enabled_for_backorder';

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var ProductAlertHelper
     */
    protected $productAlertHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StockResolver
     */
    protected $stockResolver;

    /**
     * @var array
     */
    protected $backOrderAllowedProducts = [
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
    ];

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param HttpContext $httpContext
     * @param StoreManagerInterface $storeManager
     * @param StockResolver $stockResolver
     * @param ProductAlertHelper $productAlertHelper
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        StoreManagerInterface $storeManager,
        StockResolver $stockResolver,
        ProductAlertHelper $productAlertHelper
    ) {
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->productAlertHelper = $productAlertHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabledForGuest()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ENABLED_FOR_GUEST,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnabledForBackorder()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ENABLED_FOR_BACKORDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function showButton()
    {
        return !$this->isLoggedIn() && $this->isEnabledForGuest() || $this->isLoggedIn();
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH) == true;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isBackOrderAllow($product)
    {
        if (!in_array($product->getTypeId(), $this->backOrderAllowedProducts)) {
            return false;
        }

        $stock = $this->getStockProvider($product);
        if (!$stock) {
            return false;
        }

        return (bool)$stock->getBackorders() && $stock->getQty() <= 0;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getStockProvider($product)
    {
        return $this->stockResolver->getProductStockItem($product);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostAction()
    {
        return $this->storeManager->getStore()->getUrl(
            'outofstocknotif/add/stock',
            [
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->productAlertHelper->getEncodedUrl(),
            ]
        );
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->productAlertHelper->getProduct();
    }
}
