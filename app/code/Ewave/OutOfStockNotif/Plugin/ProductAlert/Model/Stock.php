<?php
namespace Ewave\OutOfStockNotif\Plugin\ProductAlert\Model;

use Magento\ProductAlert\Model\Stock as StockModel;

class Stock
{
    /**
     * Do not replace the email address
     *
     * @param StockModel $subject
     * @param array $data
     * @return array
     */
    public function beforeAddData(
        StockModel $subject,
        array $data
    ) {
        if ($subject->getEmail() && isset($data['email']) && $subject->getEmail() != $data['email']) {
            return [[]];
        }
        return [$data];
    }
}
