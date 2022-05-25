<?php

namespace Marketplacer\Brand\Block\Adminhtml\Brand\Edit;

use Magento\Backend\Block\Widget\Context;
use Marketplacer\Brand\Helper\Config;

/**
 * Class SaveButton
 * @package Marketplacer\Brand\Controller\Adminhtml\Brand\Edit
 */
class SaveAndContinueButton extends \Marketplacer\Base\Block\Adminhtml\Entity\Edit\SaveAndContinueButton
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

        return parent::getButtonData();
    }
}
