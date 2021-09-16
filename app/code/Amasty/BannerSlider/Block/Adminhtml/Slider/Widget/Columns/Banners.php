<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Adminhtml\Slider\Widget\Columns;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Slider;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Widget\Block\BlockInterface;

class Banners extends AbstractRenderer implements BlockInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if ($this->getColumn()->getEditable()) {
            $result = '<div class="admin__grid-control">';
            $result .= $this->getColumn()->getEditOnly() ? ''
                : '<span class="admin__grid-control-value">' . $this->_getValue($row) . '</span>';

            return $result . $this->_getInputValueElement($row) . '</div>';
        }
        return $this->prepareValue($row);
    }

    private function prepareValue(DataObject $row): string
    {
        $bannerLinks = [];
        $bannerIds = $row->getData(Slider::BANNER_IDS);
        $bannerNames = $row->getData(Slider::BANNER_NAMES);

        if ($bannerIds && $bannerNames) {
            $bannerIds = is_string($bannerIds) ? explode(',', $bannerIds) : $bannerIds;
            $bannerNames = is_string($bannerNames) ? explode(',', $bannerNames) : $bannerNames;

            foreach ($bannerIds as $key => $id) {
                $url = $this->urlBuilder->getUrl('ambannerslider/banner/edit', [BannerInterface::ID => $id]);
                $bannerName = $this->_escaper->escapeHtml($bannerNames[$key]);
                $bannerLinks[] = sprintf('<a href="%s">%s</a>', $url, $bannerName);
            }
        }

        return implode(', ', $bannerLinks);
    }
}
