<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Information extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var string
     */
    private $userGuide = 'https://amasty.com/docs/doku.php?id=magento_2:banner-slider';

    /**
     * @var array
     */
    private $enemyExtensions = [];

    /**
     * @var string
     */
    private $content;

    public function render(AbstractElement $element): string
    {
        $html = $this->_getHeaderHtml($element);
        $content = __('Please update Amasty Base module. Re-upload it and replace all the files.')->getText();
        $this->setContent($content);
        $this->_eventManager->dispatch(
            'amasty_base_add_information_content',
            ['block' => $this]
        );
        $html .= $this->getContent();
        $html .= $this->_getFooterHtml($element);
        $html = str_replace(
            'amasty_information]" type="hidden" value="0"',
            'amasty_information]" type="hidden" value="1"',
            $html
        );
        $html = preg_replace('(onclick=\"Fieldset.toggleCollapse.*?\")', '', $html);

        return $html;
    }

    public function getUserGuide(): string
    {
        return $this->userGuide;
    }

    public function setUserGuide(string $userGuide)
    {
        $this->userGuide = $userGuide;
    }

    public function getEnemyExtensions(): array
    {
        return $this->enemyExtensions;
    }

    public function setEnemyExtensions(array $enemyExtensions)
    {
        $this->enemyExtensions = $enemyExtensions;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }
}
