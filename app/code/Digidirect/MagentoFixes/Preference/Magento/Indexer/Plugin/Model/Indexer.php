<?php
namespace Digidirect\MagentoFixes\Preference\Magento\Indexer\Plugin\Model;

/**
 * Class Indexer
 *
 * @package Digidirect\MagentoFixes\Preference\Magento\Indexer\Plugin\Model
 */
class Indexer
{
    /**
     * Check if indexer is working and block invalidate
     *
     * @param \Magento\Indexer\Model\Indexer $subject
     * @param \Closure $proceed
     * @return void
     */
    public function aroundInvalidate(\Magento\Indexer\Model\Indexer $subject, \Closure $proceed)
    {
        if ($subject->isWorking()) {
            $subject->getState()->setIsNeedInvalid(true);
            $subject->getState()->save();
            return;
        }

        return $proceed();
    }
}
