<?php


namespace Ewave\Vii\Model\Processor\Quote;

use Ewave\Vii\Service\Config\Config;

/**
 * Class VisitorCleanupProcess
 * @package Ewave\Vii\Model\Processor\Quote
 */
class VisitorCleanupProcess extends \Ewave\Vii\Model\Processor\Quote\CleanupProcess
{
    const PROCESS_CODE = 'ewave_vii_quote_visitor_cleanup';

    /**
     * @var \Ewave\Vii\Model\ResourceModel\Customer\Visitor
     */
    protected $visitor;

    /**
     * VisitorCleanupProcess constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     * @param \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository
     * @param Config $config
     * @param \Ewave\Vii\Model\ResourceModel\Customer\Visitor $visitor
     * @param null $initParams
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface $viiGiftCardEntityRepository,
        Config $config,
        \Ewave\Vii\Model\ResourceModel\Customer\Visitor $visitor,
        $initParams = null
    ) {
        parent::__construct(
            $quoteRepository,
            $giftCAHelper,
            $giftcardaccountFactory,
            $abstractGiftCardEntityRepository,
            $viiGiftCardEntityRepository,
            $config,
            $initParams
        );
        $this->visitor = $visitor;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $result = parent::process();
        $requestParameters = $this->getRunOptions();
        $quoteId = $requestParameters['quote'] ?? null;
        if (is_numeric($quoteId)) {
            $this->visitor->deleteVisitorDataByQuoteId($quoteId);
        }
        return $result;
    }
}
