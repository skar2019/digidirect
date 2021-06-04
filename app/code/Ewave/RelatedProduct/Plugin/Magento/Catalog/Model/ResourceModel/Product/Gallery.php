<?php
namespace Ewave\RelatedProduct\Plugin\Magento\Catalog\Model\ResourceModel\Product;

use Ewave\RelatedProduct\Setup\InstallSchema;
use Magento\Framework\DB\Select;

/**
 * Class Gallery
 * @author Ewave team
 * @package Ewave\RelatedProduct\Plugin\Magento\Catalog\Model\ResourceModel\Product
 */
class Gallery
{
    /**
     * @param string $subject
     * @param Select $select
     * @return mixed
     */
    public function afterCreateBatchBaseSelect($subject, Select $select)
    {
        $select->joinLeft(
            ['custom_attributes' => $subject->getTable(InstallSchema::CUSTOM_GALLERY_VALUE_TABLE)],
            new \Zend_Db_Expr($subject->getMainTableAlias() . '.value_id = custom_attributes.value_id'),
            ['featured_product_image']
        );
        return $select;
    }
}
