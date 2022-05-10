<?php

declare(strict_types=1);

namespace Amasty\BannerSliderGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection as WidgetCollection;

class GetSliderWidget implements ResolverInterface
{
    /**
     * @var WidgetCollection
     */
    private $widgetCollection;

    /**
     * @var SliderDataProvider
     */
    private $sliderDataProvider;

    public function __construct(
        WidgetCollection $widgetCollection,
        SliderDataProvider $sliderDataProvider
    ) {
        $this->widgetCollection = $widgetCollection;
        $this->sliderDataProvider = $sliderDataProvider;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $data = [];
        try {
            $widget = $this->widgetCollection->getItemById($args['id']);
            if ($widget) {
                $widgetParams = $widget->getWidgetParameters();
                $data = [
                    'show_name' => (bool)$widgetParams['show_name'] ?? false,
                    'alignment' => (string)$widgetParams['alignment'] ?? '',
                    'name' => $widget->getTitle(),
                    'slider' => $this->sliderDataProvider->execute(
                        (int)$widgetParams['slider_id'],
                        $context->getExtensionAttributes()->getStore()
                    )
                ];
            }
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }

        return $data;
    }
}
