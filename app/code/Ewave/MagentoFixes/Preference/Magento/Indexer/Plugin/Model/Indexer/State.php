<?php
namespace Ewave\MagentoFixes\Preference\Magento\Indexer\Plugin\Model\Indexer;

use Magento\Framework\Indexer\StateInterface;

/**
 * Class State
 *
 * @package Ewave\MagentoFixes\Preference\Magento\Indexer\Plugin\Model\Indexer
 */
class State
{
    /**
     * Check if is need invalidate index when it marked as valid
     *
     * @param \Magento\Indexer\Model\Indexer\State $subject
     * @param \Magento\Indexer\Model\Indexer\State $result
     * @return \Magento\Indexer\Model\Indexer\State
     */
    public function afterSetStatus(\Magento\Indexer\Model\Indexer\State $subject, $result)
    {
        if ($subject->getStatus() == StateInterface::STATUS_VALID && $subject->getIsNeedInvalid()) {
            $subject->setIsNeedInvalid(false);
            $subject->setStatus(StateInterface::STATUS_INVALID);
        }

        return $result;
    }
}
