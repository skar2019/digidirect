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

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class State
 * @package Mageplaza\ProductLabels\Ui\Component\Listing\Columns
 */
class State extends Column
{
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
            foreach ($dataSource['data']['items'] as & $item) {
                $currentDate = date('d-m-Y h:m:s');
                $dateFrom    = $item['from_date'];
                $dateTo      = $item['to_date'];

                if ((strtotime($dateTo) >= strtotime($currentDate) && strtotime($dateFrom) <= strtotime($currentDate))
                    || (!$dateTo && strtotime($dateFrom) <= strtotime($currentDate))) {
                    $item[$this->getData('name')] = __('running');
                } elseif (strtotime($currentDate) < strtotime($dateFrom)) {
                    $item[$this->getData('name')] = __('queue');
                } elseif (strtotime($currentDate) > strtotime($dateTo)) {
                    $item[$this->getData('name')] = __('done');
                }
            }
        }

        return $dataSource;
    }
}
