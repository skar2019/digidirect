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

namespace Mageplaza\ProductLabels\Plugin\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class MetadataProvider
 * @package Mageplaza\ProductLabels\Plugin\Model\Export
 */
class MetadataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @param RequestInterface $request
     * @param RuleFactory $rule
     */
    public function __construct(
        RequestInterface $request,
        RuleFactory $rule
    ) {
        $this->request = $request;
        $this->ruleFactory = $rule;
    }

    /**
     * @param \Magento\Ui\Model\Export\MetadataProvider $subject
     * @param callable $proceed
     * @param DocumentInterface $document
     * @param $fields
     * @param $options
     *
     * @return array
     */
    public function aroundGetRowData(
        \Magento\Ui\Model\Export\MetadataProvider $subject,
        callable $proceed,
        DocumentInterface $document,
        $fields,
        $options
    ) {
        $nameSpace = $this->request->getParam('namespace');
        $row = [];
        $result = $proceed($document, $fields, $options);
        $rule = $this->ruleFactory->create();
        if ($nameSpace === 'mpproductlabels_rule_listing') {
            foreach ($fields as $column) {
                $customAttribute = $document->getCustomAttribute($column);
                if ($column === 'rule_id') {
                    $rule->load($customAttribute->getValue());
                }
                if ($customAttribute) {
                    if (isset($options[$column]) && $column !== 'label_image' && $column !== 'list_image' && $column !== 'state') {
                        $key = $customAttribute->getValue();
                        if (isset($options[$column][$key])) {
                            $row[] = $options[$column][$key];
                        } else {
                            $row[] = '';
                        }
                    } else if ($column === 'label_image') {
                        $row[] = $rule->getLabelImage() ?: $rule->getLabelTemplate();
                    } else if ($column === 'list_image') {
                        $row[] = $rule->getListImage() ?: $rule->getListTemplate();
                    } else if ($column === 'state') {
                        $currentDate = date('d-m-Y h:m:s');
                        $dateFrom    = $rule->getFromDate();
                        $dateTo      = $rule->getToDate();
                        if ((strtotime($dateTo) >= strtotime($currentDate) && strtotime($dateFrom) <= strtotime($currentDate))
                            || (!$dateTo && strtotime($dateFrom) <= strtotime($currentDate))) {
                            $row[] = __('running');
                        } elseif (strtotime($currentDate) < strtotime($dateFrom)) {
                            $row[] = __('queue');
                        } elseif (strtotime($currentDate) > strtotime($dateTo)) {
                            $row[] = __('done');
                        }
                    }
                    else {
                        $row[] = $customAttribute->getValue();
                    }
                }
            }
        }

        return !empty($row) ? $row : $result;
    }
}
