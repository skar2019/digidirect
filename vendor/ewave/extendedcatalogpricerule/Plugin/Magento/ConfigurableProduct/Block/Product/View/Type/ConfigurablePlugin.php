<?php
namespace Ewave\ExtendedCatalogPriceRule\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

use Ewave\ExtendedCatalogPriceRule\Api\Data\RuleDisplayMessageInterface;
use Ewave\ExtendedCatalogPriceRule\Helper\ViewData;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ConfigurablePlugin
 * @package Ewave\ExtendedCatalogPriceRule\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type
 */
class ConfigurablePlugin
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ViewData
     */
    protected $helper;

    /**
     * ConfigurablePlugin constructor.
     * @param ViewData $helper
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ViewData $helper,
        SerializerInterface $serializer
    ) {
        $this->helper = $helper;
        $this->serializer = $serializer;
    }

    /**
     * @param Configurable $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonConfig(Configurable $subject, $result)
    {
        $decodedConfig = $this->serializer->unserialize($result);
        $productIds = array_keys($decodedConfig['index']);

        $rulesData = $this->helper->getExtendedRulesDataForView($productIds);
        if (empty($rulesData)) {
            return $result;
        }

        $decodedConfig['extended_rules'] = $rulesData;
        $forParent = $this->collectGivenExtendedRulesForParent($rulesData);
        if ($forParent && ($parentId = $decodedConfig['productId'] ?? null)) {
            $decodedConfig['extended_rules'][$parentId] = $forParent;
        }

        return $this->serializer->serialize($decodedConfig);
    }

    /**
     * @param array $rulesData
     * @return array|bool
     */
    protected function collectGivenExtendedRulesForParent(array $rulesData)
    {
        $simpleProductsNumber = count($rulesData);
        $rulesDataCount = count(array_filter($rulesData));
        if ($rulesDataCount !== $simpleProductsNumber) { // some children don't have any extended rules at all.
            return false;
        }

        // Count all extended rules for children.
        $ruleIdsCounter = [];
        $rulesDataForParent = [];
        foreach ($rulesData as $productId => $data) {
            foreach ($data as $key => $value) {
                $ruleIdsCounter[$key] = ($ruleIdsCounter[$key] ?? null) ? ++$ruleIdsCounter[$key] : 1;

                if (empty($rulesDataForParent[$key])) {
                    $rulesDataForParent[$key] = $value;
                }
            }
        }

        // remove rules which do not applied for all children.
        foreach ($ruleIdsCounter as $rule => $count) {
            if ($count != $simpleProductsNumber) {
                unset($rulesDataForParent[$rule]);
            }
        }
        return $rulesDataForParent;
    }
}
