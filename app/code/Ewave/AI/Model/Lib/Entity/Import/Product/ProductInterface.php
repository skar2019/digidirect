<?php
namespace Ewave\AI\Model\Lib\Entity\Import\Product;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface ProductInterface extends ImportInterface
{
    /**
     * @param string|array $sku
     * @return bool
     */
    public function delete($sku);
}
