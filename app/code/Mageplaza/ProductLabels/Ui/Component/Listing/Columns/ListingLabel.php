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

namespace Mageplaza\ProductLabels\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\ProductLabels\Helper\Image as HelperImage;

/**
 * Class ListingLabel
 * @package Mageplaza\ProductLabels\Ui\Component\Listing\Columns
 */
class ListingLabel extends Column
{
    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * ListingLabel constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HelperImage $helperImage
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HelperImage $helperImage,
        array $components = [],
        array $data = []
    ) {
        $this->_helperImage = $helperImage;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $data = $item[$fieldName];
                if ($data) {
                    $src = $this->_helperImage->getBaseMediaUrl() . '/' .
                        $this->_helperImage->getBaseMediaPath(HelperImage::TEMPLATE_MEDIA_LISTING_LABEL) . '/' .
                        trim($data, '/');
                } else {
                    $src = '';
                }
                $item[$fieldName] = $src;
            }
        }

        return $dataSource;
    }
}
