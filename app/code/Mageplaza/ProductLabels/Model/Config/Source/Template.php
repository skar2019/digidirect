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
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\ProductLabels\Block\Label;

/**
 * Class Template
 * @package Mageplaza\ProductLabels\Model\Config\Source
 */
class Template implements ArrayInterface
{
    const TEMPLATE1      = 'p1/PL_Final_P1-01.png';
    const TEMPLATE2      = 'p1/PL_Final_P1-02.png';
    const TEMPLATE3      = 'p1/PL_Final_P1-03.png';
    const TEMPLATE4      = 'p1/PL_Final_P1-04.png';
    const TEMPLATE5      = 'p1/PL_Final_P1-05.png';
    const TEMPLATE6      = 'p1/PL_Final_P1-06.png';
    const TEMPLATE7      = 'p1/PL_Final_P1-07.png';
    const TEMPLATE8      = 'p1/PL_Final_P1-08.png';
    const TEMPLATE9      = 'p1/PL_Final_P1-09.png';
    const TEMPLATE10     = 'p1/PL_Final_P1-10.png';
    const TEMPLATE11     = 'p1/PL_Final_P1-11.png';
    const TEMPLATE12     = 'p1/PL_Final_P1-12.png';
    const TEMPLATE13     = 'p1/PL_Final_P1-13.png';
    const TEMPLATE14     = 'p1/PL_Final_P1-14.png';
    const TEMPLATE15     = 'p1/PL_Final_P1-15.png';
    const TEMPLATE16     = 'p1/PL_Final_P1-16.png';
    const TEMPLATE17     = 'p1/PL_Final_P1-17.png';
    const TEMPLATE18     = 'p1/PL_Final_P1-18.png';
    const TEMPLATE19     = 'p1/PL_Final_P1-19.png';
    const TEMPLATE20     = 'p1/PL_Final_P1-20.png';
    const TEMPLATE21     = 'p1/PL_Final_P1-21.png';
    const TEMPLATE22     = 'p1/PL_Final_P1-22.png';
    const TEMPLATE23     = 'p1/PL_Final_P1-23.png';
    const TEMPLATE24     = 'p1/PL_Final_P1-24.png';
    const TEMPLATE25     = 'p1/PL_Final_P1-25.png';
    const TEMPLATE26     = 'p1/PL_Final_P1-26.png';
    const TEMPLATE27     = 'p1/PL_Final_P1-27.png';
    const TEMPLATE_P2_1  = 'p2/PL_Final_P2-01.png';
    const TEMPLATE_P2_2  = 'p2/PL_Final_P2-02.png';
    const TEMPLATE_P2_3  = 'p2/PL_Final_P2-03.png';
    const TEMPLATE_P2_4  = 'p2/PL_Final_P2-04.png';
    const TEMPLATE_P2_5  = 'p2/PL_Final_P2-05.png';
    const TEMPLATE_P2_6  = 'p2/PL_Final_P2-06.png';
    const TEMPLATE_P2_7  = 'p2/PL_Final_P2-07.png';
    const TEMPLATE_P2_8  = 'p2/PL_Final_P2-08.png';
    const TEMPLATE_P2_9  = 'p2/PL_Final_P2-09.png';
    const TEMPLATE_P2_10 = 'p2/PL_Final_P2-10.png';
    const TEMPLATE_P2_11 = 'p2/PL_Final_P2-11.png';
    const TEMPLATE_P2_12 = 'p2/PL_Final_P2-12.png';
    const TEMPLATE_P2_13 = 'p2/PL_Final_P2-13.png';
    const TEMPLATE_P2_14 = 'p2/PL_Final_P2-14.png';
    const TEMPLATE_P2_15 = 'p2/PL_Final_P2-15.png';
    const TEMPLATE_P2_16 = 'p2/PL_Final_P2-16.png';
    const TEMPLATE_P2_17 = 'p2/PL_Final_P2-17.png';
    const TEMPLATE_P2_18 = 'p2/PL_Final_P2-18.png';
    const TEMPLATE_P2_19 = 'p2/PL_Final_P2-19.png';
    const TEMPLATE_P2_20 = 'p2/PL_Final_P2-20.png';
    const TEMPLATE_P2_21 = 'p2/PL_Final_P2-21.png';
    const TEMPLATE_P2_22 = 'p2/PL_Final_P2-22.png';
    const TEMPLATE_P3_2  = 'p3/PL_Final_P3-02.png';
    const TEMPLATE_P3_3  = 'p3/PL_Final_P3-03.png';
    const TEMPLATE_P3_4  = 'p3/PL_Final_P3-04.png';
    const TEMPLATE_P3_5  = 'p3/PL_Final_P3-05.png';
    const TEMPLATE_P3_6  = 'p3/PL_Final_P3-06.png';
    const TEMPLATE_P3_7  = 'p3/PL_Final_P3-07.png';
    const TEMPLATE_P3_8  = 'p3/PL_Final_P3-08.png';

    /**
     * @var Label
     */
    protected $_label;

    /**
     * Template constructor.
     *
     * @param Label $label
     */
    public function __construct(Label $label)
    {
        $this->_label = $label;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('-- Please select --')],
            ['value' => self::TEMPLATE17, 'label' => __('Sale 1')],
            ['value' => self::TEMPLATE18, 'label' => __('Hot')],
            ['value' => self::TEMPLATE20, 'label' => __('Hot Deal 1')],
            ['value' => self::TEMPLATE21, 'label' => __('Costumer Pick 1')],
            ['value' => self::TEMPLATE22, 'label' => __('Free Shipping 1')],
            ['value' => self::TEMPLATE23, 'label' => __('New Arrival 1')],
            ['value' => self::TEMPLATE_P2_14, 'label' => __('Sale 2')],
            ['value' => self::TEMPLATE_P2_19, 'label' => __('Free Shipping 2')],
            ['value' => self::TEMPLATE_P2_3, 'label' => __('Fuzzy Wuzzy 1')],
            ['value' => self::TEMPLATE4, 'label' => __('Dogwood Rose')],
            ['value' => self::TEMPLATE1, 'label' => __('Deep Skyblue 1')],
            ['value' => self::TEMPLATE2, 'label' => __('Blue Violet ')],
            ['value' => self::TEMPLATE3, 'label' => __('CG Red')],
            ['value' => self::TEMPLATE5, 'label' => __('Deep Skyblue 2')],
            ['value' => self::TEMPLATE6, 'label' => __('Cadmium Red')],
            ['value' => self::TEMPLATE7, 'label' => __('Citrine')],
            ['value' => self::TEMPLATE8, 'label' => __('Coral 1')],
            ['value' => self::TEMPLATE9, 'label' => __('Seagreen')],
            ['value' => self::TEMPLATE10, 'label' => __('Deep Pink')],
            ['value' => self::TEMPLATE11, 'label' => __('Aquamarine')],
            ['value' => self::TEMPLATE12, 'label' => __('Dark Red')],
            ['value' => self::TEMPLATE13, 'label' => __('Green Yellow')],
            ['value' => self::TEMPLATE14, 'label' => __('Brick Red')],
            ['value' => self::TEMPLATE15, 'label' => __('New')],
            ['value' => self::TEMPLATE16, 'label' => __('Limited')],
            ['value' => self::TEMPLATE19, 'label' => __('Best Seller 1')],
            ['value' => self::TEMPLATE24, 'label' => __('Limited Edition 1')],
            ['value' => self::TEMPLATE25, 'label' => __('Sales')],
            ['value' => self::TEMPLATE26, 'label' => __('20% Off 1')],
            ['value' => self::TEMPLATE27, 'label' => __('Most Popular 1')],
            ['value' => self::TEMPLATE_P2_1, 'label' => __('Fuzzy Wuzzy 2')],
            ['value' => self::TEMPLATE_P2_2, 'label' => __('Deep Skyblue 3')],
            ['value' => self::TEMPLATE_P2_4, 'label' => __('Cyber Yellow')],
            ['value' => self::TEMPLATE_P2_5, 'label' => __('Coral 2')],
            ['value' => self::TEMPLATE_P2_6, 'label' => __('Citron Green')],
            ['value' => self::TEMPLATE_P2_7, 'label' => __('Deep Cerise')],
            ['value' => self::TEMPLATE_P2_8, 'label' => __('Free Shipping 3')],
            ['value' => self::TEMPLATE_P2_9, 'label' => __('24h Limited')],
            ['value' => self::TEMPLATE_P2_10, 'label' => __('Dark Red 2')],
            ['value' => self::TEMPLATE_P2_11, 'label' => __('Dark Spring Green')],
            ['value' => self::TEMPLATE_P2_12, 'label' => __('Best Seller 2')],
            ['value' => self::TEMPLATE_P2_13, 'label' => __('Most Popular 2')],
            ['value' => self::TEMPLATE_P2_15, 'label' => __('20% Off 2')],
            ['value' => self::TEMPLATE_P2_16, 'label' => __('New Arrival 2')],
            ['value' => self::TEMPLATE_P2_17, 'label' => __('Staff Pick 1')],
            ['value' => self::TEMPLATE_P2_18, 'label' => __('Costumer Pick 2')],
            ['value' => self::TEMPLATE_P2_20, 'label' => __('Limited Edition 2')],
            ['value' => self::TEMPLATE_P2_21, 'label' => __('Hot Deal 2')],
            ['value' => self::TEMPLATE_P2_22, 'label' => __('Staff Pick 2')],
            ['value' => self::TEMPLATE_P3_2, 'label' => __('Fire Engine Red 1')],
            ['value' => self::TEMPLATE_P3_3, 'label' => __('Fire Engine Red 2')],
            ['value' => self::TEMPLATE_P3_5, 'label' => __('Fire Engine Red 3')],
            ['value' => self::TEMPLATE_P3_6, 'label' => __('Fire Engine Red 4')],
            ['value' => self::TEMPLATE_P3_7, 'label' => __('Fire Engine Red 5')],
            ['value' => self::TEMPLATE_P3_8, 'label' => __('Fire Engine Red 6')],
        ];
    }
}
