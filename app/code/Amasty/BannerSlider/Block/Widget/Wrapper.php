<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Widget;

class Wrapper extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toHtml(): string
    {
        if ($this->moduleManager->isEnabled('Amasty_BannerSlider')) {
            $widget = $this->getLayout()->createBlock(
                Slider::class
            )->setData(
                $this->getData()
            );

            $html = $widget->toHtml();
        }

        return $html ?? '';
    }
}
