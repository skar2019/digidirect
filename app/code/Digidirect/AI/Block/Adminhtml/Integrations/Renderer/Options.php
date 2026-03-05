<?php
namespace Digidirect\AI\Block\Adminhtml\Integrations\Renderer;

use Magento\Framework\Api\ObjectFactory;

/**
 * Class Options
 * @package Digidirect\AI\Block\Adminhtml\Integrations\Renderer
 */
class Options extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ObjectFactory
     */
    protected $objectFactory = null;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Options constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectFactory $objectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ObjectFactory $objectFactory,
        array $data = []
    ) {
        $this->objectFactory = $objectFactory;
        $this->setTemplate('integrations/renderer/options.phtml');
        parent::__construct($context, $data);
    }

    /**
     * @param mixed $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getSelectOptions($code)
    {
        return $this->objectFactory->get($code)->toOptionArray();
    }
}
