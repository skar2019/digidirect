<?php
namespace Ewave\Collect\Plugin\Quote\Model;

use Magento\Quote\Model\Quote as Subject;

class Quote
{
    /**
     * @param Subject $subject
     * @param Subject $result
     * @param Subject $quote
     * @return Subject
     */
    public function afterMerge(Subject $subject, $result, Subject $quote)
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            foreach ($subject->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $quoteItem->setCollectPlaceId($item->getCollectPlaceId());
                    $quoteItem->setCollectPlaceStorageName($item->getCollectPlaceStorageName());
                    break;
                }
            }
        }
        return $result;
    }
}
