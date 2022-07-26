<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Controller\Adminhtml\Notification;

use MageSpark\Base\Model\Feed;
use Magento\Backend\App\Action;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use MageSpark\Base\Model\Source\Frequency as SourceFrequency;

/**
 * Class Frequency
 * @package MageSpark\Base\Controller\Adminhtml\Notification
 */
class Frequency extends Action
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var SourceFrequency
     */
    private $frequency;

    /**
     * Frequency constructor.
     *
     * @param Action\Context $context
     * @param ConfigInterface $config
     * @param ReinitableConfigInterface $reinitableConfig
     * @param WriterInterface $configWriter
     * @param SourceFrequency $frequency
     */
    public function __construct(
        Action\Context $context,
        ConfigInterface $config,
        ReinitableConfigInterface $reinitableConfig,
        WriterInterface $configWriter,
        SourceFrequency $frequency
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->frequency = $frequency;
    }

    /**
     * Set the Frequency url
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $action = $this->getRequest()->getParam('action');
        switch ($action) {
            case 'less':
                $this->increaseFrequency();
                break;
            case 'more':
                $this->decreaseFrequency();
                break;
            default:
                $this->messageManager->addErrorMessage(
                    __(
                        'An error occurred while changing the frequency.'
                    )
                );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * Its allow the authorization
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageSpark_Base::config'
        );
    }

    /**
     * Decrease the Frequency
     */
    private function decreaseFrequency()
    {
        $currentValue = $this->getCurrentValue();
        $allValues = $this->frequency->toOptionArray();
        $resultValue = null;
        foreach ($allValues as $option) {
            if ($option['value'] != $currentValue) {
                $resultValue = $option['value'];
            } else {
                if ($resultValue) {
                    $this->changeFrequency($resultValue);
                }

                break;
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'You will get more messages of this type. Notification frequency has been updated.'
            )
        );
    }

    /**
     * Increases the Frequency
     *
     */
    private function increaseFrequency()
    {
        $currentValue = $this->getCurrentValue();
        $allValues = $this->frequency->toOptionArray();
        $resultValue = null;
        foreach ($allValues as $option) {
            if ($option['value'] == $currentValue) {
                $resultValue = $option['value'];
            }

            if ($resultValue && $option['value'] != $resultValue) {
                $this->changeFrequency($option['value']);//save next option
                break;
            }
        }

        $this->messageManager->addSuccessMessage(
            __(
                'You will get less messages of this type. Notification frequency has been updated.'
            )
        );
    }
    /*
     * Changes the Frequency
     *
     */
    private function changeFrequency($value)
    {
        $this->configWriter->save(Feed::XML_FREQUENCY_PATH, $value);
        $this->reinitableConfig->reinit();

        return $this;
    }

    /**
     * Get the current value
     *
     * @return mixed
     */
    private function getCurrentValue()
    {
        return $this->config->getValue(Feed::XML_FREQUENCY_PATH);
    }
}
