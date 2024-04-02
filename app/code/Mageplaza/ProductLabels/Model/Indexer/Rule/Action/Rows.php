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

namespace Mageplaza\ProductLabels\Model\Indexer\Rule\Action;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductLabels\Model\Indexer\Rule\AbstractAction;

/**
 * Class Rows
 * @package Mageplaza\ProductLabels\Model\Indexer\Rule\Action
 */
class Rows extends AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     *
     * @return void
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new InputException(__('Bad value was supplied.'));
        }
        try {
            $this->doReindexByIds($ids);
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
