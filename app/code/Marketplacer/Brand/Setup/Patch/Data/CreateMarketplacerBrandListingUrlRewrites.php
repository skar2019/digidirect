<?php

namespace Marketplacer\Brand\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessor as BrandUrlProcessor;

class CreateMarketplacerBrandListingUrlRewrites implements DataPatchInterface
{
    /**
     * @var BrandUrlProcessor
     */
    protected $brandUrlProcessor;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        BrandUrlProcessor $brandUrlProcessor
    ) {
        $this->brandUrlProcessor = $brandUrlProcessor;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->brandUrlProcessor->processBrandListingUrlRewrites();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
