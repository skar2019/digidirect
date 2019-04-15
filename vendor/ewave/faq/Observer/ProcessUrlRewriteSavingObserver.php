<?php

namespace Ewave\Faq\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Exception\LocalizedException;
use Ewave\Faq\Model\Category;
use Ewave\Faq\Helper\Data as Helper;
use Ewave\Faq\Model\FaqCategoryUrlRewriteGenerator as UrlGenerator;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var UrlGenerator
     */
    protected $urlRewriteGenerator;

    /**
     * ProcessUrlRewriteSavingObserver constructor.
     *
     * @param UrlPersistInterface $urlPersist
     * @param Helper $helper
     * @param UrlGenerator $urlRewriteGenerator
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        Helper $helper,
        UrlGenerator $urlRewriteGenerator
    ) {
        $this->urlPersist = $urlPersist;
        $this->helper = $helper;
        $this->urlRewriteGenerator = $urlRewriteGenerator;
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
            $urls = $this->urlRewriteGenerator->generate($faqCategory);

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $faqCategory->getId(),
                UrlRewrite::ENTITY_TYPE => Category::URL_REWRITE_ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        }
    }
}
