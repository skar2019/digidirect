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
    protected $_template = 'Mageplaza_Shopbybrand::widget/brandcategorylist.phtml';

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

        $str = $this->getData('category_id');
        $sql = 'main_table.cat_id IN (' . $str . ')';
        $result = [];
        $brands = $this->_categoryFactory->create()->getCategoryCollection($sql, null)->getData();
        $cameras = array(137,120,8253,134,135,138,139,245,291,483);
        $lenses = array(137,120,8253,134,135,138,139,223,517,221);
        $drones = array(353,541,19521,7177,535,9307,12287,12269);
        $lightning = array(495,7105,13345,271,445,461,293,231,463,9364);
        $optics = array(134,139,13321,273,355,243,8253,135,507,567);
        $audiovisual = array(281,18988,19465,7095,9405,9370,315,137,135);
        $provideo = array(315,120,137,138,333,10265,305,19569,10179,19918);
        $smarthome = array(9256,19563,7153,19888,19984,12347,14558,14501,18775,19891);
        $computers = array(433,9214,10346,14420,13465,19533,19830,583,7159,9241);
        foreach ($brands as $brand => $item) {
            switch ($str) {
                case "1":
                   if (in_array($item['option_id'], $cameras)){
                       $result[] = $item['option_id'];
                   }
                   break;
                case "4":
                    if (in_array($item['option_id'], $lenses)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "7":
                    if (in_array($item['option_id'], $drones)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "10":
                    if (in_array($item['option_id'], $lightning)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "16":
                    if (in_array($item['option_id'], $optics)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "19":
                    if (in_array($item['option_id'], $audiovisual)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "22":
                    if (in_array($item['option_id'], $provideo)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "25":
                    if (in_array($item['option_id'], $smarthome)){
                        $result[] = $item['option_id'];
                    }
                    break;
                case "28":
                    if (in_array($item['option_id'], $computers)){
                        $result[] = $item['option_id'];
                    }
                    break;
                default:
                    $result[] = $item['option_id'];
            }


        }

        return implode(',', array_unique($result));
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
