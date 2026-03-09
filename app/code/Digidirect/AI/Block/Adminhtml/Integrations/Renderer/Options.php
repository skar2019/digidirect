<?php
namespace Digidirect\AI\Block\Adminhtml\Integrations\Renderer;

use Magento\Config\Model\Config\SourceFactory;

/**
 * Class Options
 * @package Digidirect\AI\Block\Adminhtml\Integrations\Renderer
 */
class Options extends \Magento\Framework\View\Element\Template
{
    /**
     * Source Factory
     *
     * @var SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Options constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param SourceFactory $sourceFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SourceFactory $sourceFactory,
        array $data = []
    ) {
        $this->sourceFactory = $sourceFactory;
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
        return $this->sourceFactory->create($code)->toOptionArray();
    }
}
