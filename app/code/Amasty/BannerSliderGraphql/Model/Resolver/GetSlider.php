<?php

declare(strict_types=1);

namespace Amasty\BannerSliderGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetSlider implements ResolverInterface
{
    /**
     * @var SliderDataProvider
     */
    private $sliderDataProvider;

    public function __construct(
        SliderDataProvider $sliderDataProvider
    ) {
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
        try {
            $data = $this->sliderDataProvider->execute(
                (int)$args['id'],
                $context->getExtensionAttributes()->getStore()
            );
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }

        return $data;
    }
}
