<?php
/**
 * Class Cron
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AbandonedCart\Model;

/**
 * Class Cron
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Cron
{
    /**
     * QuoteFactory
     *
     * @var Sales\QuoteFactory
     */
    public $quoteFactory;

    /**
     * HelperData
     *
     * @var \Sparsh\AbandonedCart\Helper\Data
     */
    public $helper;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\Cron\CollectionFactory
     */
    public $cronCollection;

    /**
     * Cron constructor.
     *
     * @param Sales\QuoteFactory                    $quoteFactory   quoteFactory
     * @param \Sparsh\AbandonedCart\Helper\Data $helper         helper
     * @param ResourceModel\Cron\CollectionFactory  $cronCollection cronCollection
     */
    public function __construct(
        \Sparsh\AbandonedCart\Model\Sales\QuoteFactory $quoteFactory,
        \Sparsh\AbandonedCart\Helper\Data $helper,
        \Sparsh\AbandonedCart\Model\ResourceModel\Cron\CollectionFactory $cronCollection
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->helper = $helper;
        $this->cronCollection = $cronCollection;
    }

    /**
     * CRON FOR ABANDONED CARTS.
     *
     * @return null
     */
    public function abandonedCarts()
    {
        if ($this->jobHasAlreadyBeenRun('sparsh_abandoned_cart')) {
            $this->helper->log('Skipping sparsh_abandoned_cart job run');
            return;
        }
        $this->quoteFactory->create()->processAbandonedCarts();
    }

    /**
     * Check if already ran for same time
     *
     * @param string $jobCode jobCode
     *
     * @return bool
     */
    public function jobHasAlreadyBeenRun($jobCode)
    {
        $currentRunningJob = $this->cronCollection->create()
            ->addFieldToFilter('job_code', $jobCode)
            ->addFieldToFilter('status', 'running')
            ->setPageSize(1);

        if ($currentRunningJob->getSize()) {
            $jobOfSameTypeAndScheduledAtDateAlreadyExecuted =  $this->cronCollection->create()
                ->addFieldToFilter('job_code', $jobCode)
                ->addFieldToFilter('scheduled_at', $currentRunningJob->getFirstItem()->getScheduledAt())
                ->addFieldToFilter('status', ['in' => ['success', 'failed']]);

            return ($jobOfSameTypeAndScheduledAtDateAlreadyExecuted->getSize()) ? true : false;
        }

        return false;
    }
}
