<?php

namespace DigiDirect\AutoMetaDescription\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Filter\StripTags;

class SetProductMetaDescription implements ObserverInterface
{
    const META_LIMIT = 200;

    protected $registry;
    protected $pageConfig;
    protected $stripTags;

    public function __construct(
        Registry $registry,
        PageConfig $pageConfig,
        StripTags $stripTags
    ) {
        $this->registry = $registry;
        $this->pageConfig = $pageConfig;
        $this->stripTags = $stripTags;
    }

    public function execute(Observer $observer)
    {
        $product = $this->registry->registry('current_product');

        if (!$product) {
            return;
        }

        // If meta description exists for this store view, preserve it
        $existingMeta = $product->getData('meta_description');
        if ($existingMeta !== null && trim($existingMeta) !== '') {
            $this->pageConfig->setDescription(trim($existingMeta));
            return;
        }

        $productName = trim($product->getName());
        if (!$productName) {
            return;
        }

        $description = (string)$product->getDescription();
        $firstParagraph = $this->getFirstParagraph($description);

        $templatePrefix = "Shop the {$productName} at digiDirect.";
        $templateSuffix = "Fast shipping Australia wide.";

        $meta = $this->buildMeta(
            $templatePrefix,
            $firstParagraph,
            $templateSuffix
        );

        $this->pageConfig->setDescription($meta);
    }

    protected function getFirstParagraph($description)
    {
        if (empty($description)) {
            return '';
        }

        // Split by closing paragraph tag first
        if (strpos($description, '</p>') !== false) {
            $parts = explode('</p>', $description);
            $first = $parts[0];
        } else {
            // Fallback to newline
            $parts = preg_split('/\r\n|\r|\n/', $description);
            $first = $parts[0];
        }

        $clean = trim($this->stripTags->filter($first));
        return preg_replace('/\s+/', ' ', $clean);
    }

    protected function buildMeta($prefix, $paragraph, $suffix)
    {
        $prefix = trim($prefix);
        $suffix = trim($suffix);

        if (empty($paragraph)) {
            // No description available
            return $this->cleanMeta("{$prefix} {$suffix}");
        }

        // Calculate available space for paragraph
        $baseLength = strlen("{$prefix}  {$suffix}");
        $available = self::META_LIMIT - $baseLength;

        if ($available <= 0) {
            return substr($this->cleanMeta("{$prefix} {$suffix}"), 0, self::META_LIMIT);
        }

        if (strlen($paragraph) > $available) {
            $paragraph = substr($paragraph, 0, $available);

            // Avoid cutting mid-word
            $paragraph = preg_replace('/\s+\S*$/', '', $paragraph);
        }

        $meta = "{$prefix} {$paragraph}. {$suffix}";

        return $this->cleanMeta($meta);
    }

    protected function cleanMeta($meta)
    {
        $meta = preg_replace('/\s+/', ' ', $meta);
        $meta = str_replace('..', '.', $meta);
        $meta = trim($meta);

        return substr($meta, 0, self::META_LIMIT);
    }
}