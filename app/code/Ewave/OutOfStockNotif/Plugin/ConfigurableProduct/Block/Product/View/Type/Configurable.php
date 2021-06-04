<?php
namespace Ewave\OutOfStockNotif\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Configurable
{
    /**
     * @param Subject $subject
     * @param \Magento\Catalog\Model\Product[] $products
     * @return array
     */
    public function afterGetAllowProducts(
        Subject $subject,
        array $products
    ) {
        $enabledProducts = [];
        foreach ($products as $product) {
            if ($product->getStatus() == Status::STATUS_ENABLED) {
                $enabledProducts[] = $product;
            }
        }
        return $enabledProducts;
    }
}
