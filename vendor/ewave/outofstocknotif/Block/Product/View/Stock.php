<?php
namespace Ewave\OutOfStockNotif\Block\Product\View;

use Magento\Framework\View\Element\Template\Context;
use Magento\ProductAlert\Helper\Data as Helper;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;
use Ewave\OutOfStockNotif\Helper\Data as EwaveHelper;

/**
 * Class Stock
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Stock extends \Magento\ProductAlert\Block\Product\View\Stock
{
    /**
     * @var EwaveHelper
     */
    protected $ewaveHelper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Stock constructor.
     *
     * @param Context $context
     * @param Helper $helper
     * @param Registry $registry
     * @param PostHelper $coreHelper
     * @param SessionFactory $customerSession
     * @param EwaveHelper $ewaveHelper
     * @param StockRegistryInterface $stockRegistry
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        Registry $registry,
        PostHelper $coreHelper,
        SessionFactory $customerSession,
        EwaveHelper $ewaveHelper,
        JsonHelper $jsonHelper,
        StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        parent::__construct($context, $helper, $registry, $coreHelper, $data);
        $this->_isScopePrivate = true;
        $this->setData('cache_lifetime', 0);
        $this->customerSession = $customerSession;
        $this->ewaveHelper = $ewaveHelper;
        $this->stockRegistry = $stockRegistry;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $info = parent::getCacheKeyInfo();
        if (!is_array($info)) {
            $info = [];
        }
        $info['is_logged_in'] = $this->ewaveHelper->isLoggedIn();
        return $info;
    }

    /**
     * @return string
     */
    public function getPostAction()
    {
        return $this->getUrl(
            'outofstocknotif/add/stock',
            [
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->_helper->getEncodedUrl(),
            ]
        );
    }

    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        parent::setTemplate($template);
        $product = $this->getProduct();
        if ($this->_template || ($product && $product->getTypeId() == ConfigurableProduct::TYPE_CODE)) {
            $this->_template = $this->getGuestTemplate();
        }
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->getProduct();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getCustomerEmail()
    {
        if ($this->ewaveHelper->isLoggedIn()) {
            return $this->getCustomer()->getEmail();
        }
        return '';
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->getSession()->getCustomer();
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function getSession()
    {
        return $this->customerSession->create();
    }

    /**
     * @return array
     */
    public function getSwatchesSalableProductIds()
    {
        $result = [];
        $product = $this->getProduct();
        if ($product && $product->getTypeId() == ConfigurableProduct::TYPE_CODE) {
            $salableProducts = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($salableProducts as $salableProduct) {
                /** @var \Magento\Catalog\Model\Product $salableProduct */
                $stockStatus = $this->stockRegistry->getStockStatus($salableProduct->getId());
                if ($stockStatus->getStockStatus()) {
                    $result[] = $salableProduct->getId();
                }
            }
        }
        return $this->jsonHelper->serialize($result);
    }
}
