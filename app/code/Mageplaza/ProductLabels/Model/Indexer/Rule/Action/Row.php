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
class Row extends AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param null $id
     *
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new InputException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        try {
            $this->doReindexByIds([$id]);
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
