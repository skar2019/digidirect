<?php

namespace Marketplacer\Brand\Block\Adminhtml\Brand\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Marketplacer\Base\Block\Adminhtml\Entity\Edit\GenericButton;
use Marketplacer\Brand\Helper\Config;

/**
 * Class SaveButton
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param Context $context
     * @param Config $configHelper
     */
    public function __construct(Context $context, Config $configHelper)
    {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if (!$this->configHelper->isAdminEditAllowed()) {
            return [];
        }

        return [
            'label'          => __('Save Brand'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 90,
        ];
    }
}
