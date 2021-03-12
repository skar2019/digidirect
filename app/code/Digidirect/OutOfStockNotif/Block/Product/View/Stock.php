<?php
namespace Digidirect\OutOfStockNotif\Block\Product\View;

use Magento\Framework\View\Element\Template\Context;
use Magento\ProductAlert\Helper\Data as Helper;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;

/**
 * Class Stock
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Stock extends \Magento\ProductAlert\Block\Product\View\Stock
{


    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

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
     * @param StockRegistryInterface $stockRegistry
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        Registry $registry,
        PostHelper $coreHelper,
        JsonHelper $jsonHelper,
        StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        parent::__construct($context, $helper, $registry, $coreHelper, $data);
        $this->stockRegistry = $stockRegistry;
        $this->jsonHelper = $jsonHelper;
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
