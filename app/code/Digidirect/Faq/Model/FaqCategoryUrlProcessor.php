<?php

namespace Digidirect\Faq\Model;

use Digidirect\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Digidirect\Faq\Helper\Data as Helper;
use Digidirect\Faq\Model\FaqCategoryUrlRewriteGenerator as UrlGenerator;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
class FaqCategoryUrlProcessor
{
    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var UrlGenerator
     */
    private $urlRewriteGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FaqCategoryUrlProcessor constructor.
     *
     * @param UrlPersistInterface $urlPersist
     * @param Helper $helper
     * @param FaqCategoryUrlRewriteGenerator $urlRewriteGenerator
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        Helper $helper,
        UrlGenerator $urlRewriteGenerator,
        LoggerInterface $logger
    ) {
        $this->urlPersist = $urlPersist;
        $this->helper = $helper;
        $this->urlRewriteGenerator = $urlRewriteGenerator;
        $this->logger = $logger;
    }

    /**
     * @param CategoryInterface $faqCategory
     * @return bool
     */
    public function processCategory(CategoryInterface $faqCategory): bool
    {
        try {
            /** @var Category $faqCategory */

            $urls = $this->urlRewriteGenerator->generate($faqCategory);

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $faqCategory->getId(),
                UrlRewrite::ENTITY_TYPE => Category::URL_REWRITE_ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        } catch (\Throwable $exception) {
            var_dump($urls);

            echo $exception->getMessage();
            echo $exception->getFile() . $exception->getLine();
            die;
            throw new LocalizedException(__($exception->getMessage()));
            return false;
        }

        return true;
    }
}
