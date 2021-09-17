<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\Component\Listing\Columns\Slider;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->escaper = $escaper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData(SliderInterface::NAME);
                $item[$name]['edit'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'ambannerslider/slider/edit',
                        [SliderInterface::ID => $item[SliderInterface::ID]]
                    ),
                    'label' => __('Edit')
                ];
                $title = $this->escaper->escapeHtml($item[SliderInterface::NAME]);
                $item[$name]['delete'] = [
                    'href'    => $this->urlBuilder->getUrl(
                        'ambannerslider/slider/delete',
                        [SliderInterface::ID => $item[SliderInterface::ID]]
                    ),
                    'label'   => __('Delete'),
                    'confirm' => [
                        'title'   => __('Delete %1', $title),
                        'message' => __('Are you sure you wan\'t to delete a %1 link?', $title)
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
