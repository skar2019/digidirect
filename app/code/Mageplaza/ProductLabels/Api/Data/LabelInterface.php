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

namespace Mageplaza\ProductLabels\Api\Data;

/**
 * Class LabelInterface
 * @package Mageplaza\ProductLabels\Api
 */
interface LabelInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const NAME                = 'name';
    const LABEL               = 'label';
    const LABEL_TEMPLATE      = 'label_template';
    const LABEL_IMAGE         = 'label_image';
    const LABEL_FONT          = 'label_font';
    const LABEL_FONTSIZE      = 'label_font_size';
    const LABEL_COLOR         = 'label_color';
    const LABEL_CSS           = 'label_css';
    const LABEL_POSITION      = 'label_position';
    const LABEL_POSITION_GRID = 'label_position_grid';
    const LIST_LABEL          = 'list_label';
    const LIST_TEMPLATE       = 'list_template';
    const LIST_IMAGE          = 'list_image';
    const LIST_FONT           = 'list_font';
    const LIST_FONT_SIZE      = 'list_font_size';
    const LIST_COLOR          = 'list_color';
    const LIST_CSS            = 'list_css';
    const LIST_POSITION       = 'list_position';
    const LIST_POSITION_GRID  = 'list_position_gird';

    /**
     * Get Label Id
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $labelId
     *
     * @return $this
     */
    public function setId($labelId);

    /**
     * Get Label Name
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Is enable label
     *
     * @return int
     */
    public function getEnabled();

    /**
     * @param int $isEnable
     *
     * @return $this
     */
    public function setEnabled($isEnable);

    /**
     * Get Store apply label
     *
     * @return string
     */
    public function getStoreIds();

    /**
     * @param string $storeIds
     *
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get Customer Ids apply label
     *
     * @return string
     */
    public function getCustomerGroupIds();

    /**
     * @param string $customerGroupIds
     *
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * Get Priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get Label Template
     *
     * @return string
     */
    public function getLabelTemplate();

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setLabelTemplate($template);

    /**
     * Get label template
     *
     * @return string
     */
    public function getLabelImage();

    /**
     * @param int $path
     *
     * @return $this
     */
    public function setLabelImage($path);

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get Label Front
     *
     * @return string
     */
    public function getLabelFront();

    /**
     * @param string $font
     *
     * @return $this
     */
    public function setLabelFront($font);

    /**
     * Get Label Front Size
     *
     * @return string
     */
    public function getLabelFontSize();

    /**
     * @param string $fontSize
     *
     * @return $this
     */
    public function setLabelFontSize($fontSize);

    /**
     * Get Label Color
     *
     * @return string
     */
    public function getLabelColor();

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setLabelColor($color);

    /**
     * @return string
     */
    public function getLabelCss();

    /**
     * @param string $css
     *
     * @return $this
     */
    public function setLabelCss($css);

    /**
     * Get Label Position
     *
     * @return string
     */
    public function getLabelPosition();

    /**
     * @param string $position
     *
     * @return $this
     */
    public function setLabelPosition($position);

    /**
     * Get Label Position on Grid
     *
     * @return string
     */
    public function getLabelPositionGrid();

    /**
     * @param string $position
     *
     * @return $this
     */
    public function setLabelPositionGrid($position);

    /**
     * Get Category is same Product
     *
     * @return int
     */
    public function getSame();

    /**
     * @param int $isSame
     *
     * @return $this
     */
    public function setSame($isSame);

    /**
     * Get Category Label Template
     *
     * @return string
     */
    public function getListTemplate();

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setListTemplate($template);

    /**
     * Get Category Label Image
     *
     * @return string
     */
    public function getListImage();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setListImage($path);

    /**
     * Get Category Label
     *
     * @return string
     */
    public function getListLabel();

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setListLabel($label);

    /**
     * Get Category Label font
     *
     * @return string
     */
    public function getListFont();

    /**
     * @param string $front
     *
     * @return $this
     */
    public function setListFont($front);

    /**
     * Get Category label Front Size
     *
     * @return string
     */
    public function getListFontSize();

    /**
     * @param string $frontSize
     *
     * @return $this
     */
    public function setListFontSize($frontSize);

    /**
     * Get category label color
     *
     * @return string
     */
    public function getListColor();

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setListColor($color);

    /**
     * Get category label css
     *
     * @return string
     */
    public function getListCss();

    /**
     * @param string $css
     *
     * @return $this
     */
    public function setListCss($css);

    /**
     * Get category label position
     *
     * @return string
     */
    public function getListPosition();

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setListPosition($position);

    /**
     * Get category label position on grid
     *
     * @return string
     */
    public function getListPositionGrid();

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setListPositionGrid($position);

    /**
     * Get Condition Serialized
     *
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $condition
     *
     * @return $this
     */
    public function setConditionsSerialized($condition);

    /**
     * Is apply bestseller for rule bale
     *
     * @return int
     */
    public function getBestSeller();

    /**
     * @param int $isBestSeller
     *
     * @return $this
     */
    public function setBestSeller($isBestSeller);

    /**
     * Is apply new for rule bale
     *
     * @return int
     */
    public function getNew();

    /**
     * @param int $isNew
     *
     * @return $this
     */
    public function setNew($isNew);

    /**
     * Is apply on sale for rule bale
     *
     * @return int
     */
    public function getOnSale();

    /**
     * @param int $onSale
     *
     * @return $this
     */
    public function setOnSale($onSale);

    /**
     * Get limit product applied
     *
     * @return int
     */
    public function getLimit();

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setLimit($number);

    /**
     * Get Label from date
     *
     * @return string
     */
    public function getFromDate();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setFromDate($date);

    /**
     * Get Label to date
     *
     * @return string
     */
    public function getToDate();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setToDate($date);

    /**
     * Is stop process
     *
     * @return int
     */
    public function getStopProcess();

    /**
     * @param int $isStop
     *
     * @return $this
     */
    public function setStopProcess($isStop);

    /**
     * Get Label create at date
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setUpdatedAt($date);

    /**
     * Get Label update at date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);
}
