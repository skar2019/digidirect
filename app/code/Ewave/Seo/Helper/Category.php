<?php
namespace Ewave\SEO\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Category
 * @package Ewave\SEO\Helper
 */
class Category extends AbstractHelper
{
    const EWAVE_SEO_CATEGORY_AUTO_GENERATION_CONFIG_PATH = 'catalog/seo';

    /**
     * Check if metadata auto-generation is enabled
     *
     * @return bool
     */
    public function isMetaAutoGenerationEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::EWAVE_SEO_CATEGORY_AUTO_GENERATION_CONFIG_PATH . '/enable_category_metadata_generation'
        );
    }
}
