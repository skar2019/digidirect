<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Block\Widget;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Mageplaza\Shopbybrand\Model\BrandFactory;
use Mageplaza\Shopbybrand\Model\CategoryFactory;

/**
 * Class CategoryId
 *
 * @package Mageplaza\Shopbybrand\Block\Brand
 */
class CategoryId extends AbstractBrand
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Shopbybrand::widget/brandlist.phtml';

    /**
     * @type BrandFactory
     */
    protected $_brandFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * CategoryId constructor.
     *
     * @param Context $context
     * @param BrandFactory $brandFactory
     * @param CategoryFactory $categoryFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        BrandFactory $brandFactory,
        CategoryFactory $categoryFactory,
        Helper $helper
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_brandFactory = $brandFactory;

        parent::__construct($context, $helper);
    }

    /**
     * @return string
     */
    public function getOptionIds()
    {
        $top = array(137,120,8253,221,134,223,135,139,138,517,
            353,54119521,7177,535,9307,12287,12269,
            495,7105,13345,271,445,461,293,231,463,9364,
            13321,273,355,243,507,567,
            281,18988,19465,7095,9405,9370,315,
            333,10265,305,19569,10179,19918,
            9256,19563,7153,19888,19984,12347,14558,14501,18775,19891,
            433,9214,10346,14420,13465,19533,19830,583,7159,9241);

        $str = $this->getData('category_id');
        $sql = 'main_table.cat_id IN (' . $str . ')';
        $result = [];
        $brands = $this->_categoryFactory->create()->getCategoryCollection($sql, null)->getData();
        foreach ($brands as $brand => $item) {
            if (in_array($item['option_id'], $top, TRUE))
            {
                $result[] = $item['option_id'];
            }
        }
    }

    /**
     * get brand by option IDs
     *
     * @return Collection
     */
    public function getCollection()
    {
        $collection = $this->_brandFactory->create()->getBrandCollection(
            null,
            ['main_table.option_id' => ['in' => $this->getOptionIds()]]
        );

        return $collection;
    }
}
