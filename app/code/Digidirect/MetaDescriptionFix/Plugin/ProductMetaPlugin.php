<?php

namespace Digidirect\MetaDescriptionFix\Plugin;

use Magento\Framework\View\Page\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

class ProductMetaPlugin
{
    protected $pageConfig;
    protected $productRepository;
    protected $storeManager;
    protected $request;

    const META_LIMIT = 200;

    public function __construct(
        Config $pageConfig,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        $this->pageConfig = $pageConfig;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    public function afterPrepareAndRender(
        \Magento\Catalog\Helper\Product\View $subject,
        $result
    ) {
        if ($this->request->getFullActionName() !== 'catalog_product_view') {
            return $result;
        }

        $productId = $this->request->getParam('id');
        if (!$productId) {
            return $result;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $product = $this->productRepository->getById($productId, false, $storeId);

        $existingMeta = $product->getData('meta_description');

        if ($existingMeta !== null && trim($existingMeta) !== '') {
            // Respect existing meta description
            return $result;
        }

        $productName = trim($product->getName());
        $description = $product->getDescription();

        if (!$description) {
            $description = '';
        }

        // Strip HTML
        $description = trim(strip_tags($description));

        // Get first paragraph
        $paragraph = preg_split('/\r\n|\r|\n/', $description)[0] ?? '';

        $prefix = "Shop the {$productName} at digiDirect. ";
        $suffix = " Fast shipping Australia wide.";

        $available = self::META_LIMIT - strlen($prefix) - strlen($suffix);

        if (strlen($paragraph) > $available) {
            $paragraph = substr($paragraph, 0, $available);
            $paragraph = rtrim($paragraph);
        }

        $final = $prefix . $paragraph . $suffix;

        // Clean spacing
        $final = preg_replace('/\s+/', ' ', $final);
        $final = trim($final);

        $this->pageConfig->setDescription($final);

        return $result;
    }
}