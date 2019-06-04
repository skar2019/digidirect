<?php

namespace Ewave\Faq\Observer;

use Ewave\Faq\Model\FaqCategoryUrlProcessor;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Ewave\Faq\Helper\Data as Helper;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var FaqCategoryUrlProcessor
     */
    private $urlProcessor;

    /**
     * ProcessUrlRewriteSavingObserver constructor.
     *
     * @param Helper $helper
     * @param FaqCategoryUrlProcessor $categoryUrlProcessor
     */
    public function __construct(
        Helper $helper,
        FaqCategoryUrlProcessor $categoryUrlProcessor
    ) {
        $this->urlProcessor = $categoryUrlProcessor;
        $this->helper = $helper;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param EventObserver $observer
     * @throws LocalizedException
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Ewave\Faq\Model\Category $faqCategory */
        $faqCategory = $observer->getEvent()->getObject();

        if (!$this->helper->isValidPageIdentifier($faqCategory->getIdentifier())) {
            throw new LocalizedException(
                __('The page URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->helper->isNumericPageIdentifier($faqCategory->getIdentifier())) {
            throw new LocalizedException(
                __('The page URL key cannot be made of only numbers.')
            );
        }

        if ($faqCategory->dataHasChangedFor('identifier') || $faqCategory->dataHasChangedFor('store_id')) {
            $this->urlProcessor->processCategory($faqCategory);
        }
    }
}
