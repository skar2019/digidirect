<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\Component\Listing\Columns;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Amasty\BannerSlider\Model\ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Amasty\BannerSlider\Model\ImageProcessor $imageProcessor,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item = $this->prepareData($item);
            }
        }

        return $dataSource;
    }

    public function prepareData(array $item): array
    {
        $fieldName = $this->getData('name');
        if (isset($item[BannerInterface::IMAGE])) {
            $item[$fieldName . '_src'] = $this->imageProcessor->getThumbnailUrl($item[BannerInterface::IMAGE]);
            $item[$fieldName . '_alt'] = $this->getAlt($item);
            $item[$fieldName . '_orig_src'] = $item[BannerInterface::IMAGE];
        }

        return $item;
    }

    /**
     * Get Alt
     *
     * @param array $row
     *
     * @return null|string
     */
    private function getAlt($row)
    {
        $altField = $this->getData('config/altField');
        return $row[$altField] ?? null;
    }
}
