<?php

namespace Ewave\Navigation\Ui\Component\Listing\Columns\Type;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;
use Ewave\Navigation\Model\NotFilteredTypes;

class Options implements OptionSourceInterface
{
    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var NotFilteredTypes
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $currentOptions = [];

    /**
     * Options constructor.
     * @param NotFilteredTypes $collectionFactory
     * @param Escaper $escaper
     */
    public function __construct(NotFilteredTypes $collectionFactory, Escaper $escaper)
    {
        $this->collectionFactory = $collectionFactory;
        $this->escaper = $escaper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);
        array_unshift($this->options, ['label' => __('Any'), 'value' => '']);

        return $this->options;
    }

    /**
     * Generate current options
     *
     * @return void
     */
    protected function generateCurrentOptions()
    {
        $collection = $this->collectionFactory->getCollection();
        $this->currentOptions[] = ['label' => '', 'value' => ''];
        foreach ($collection as $type) {
            $name = $this->escaper->escapeHtml($type->getTypeName());
            $this->currentOptions[$name]['label'] = $name;
            $this->currentOptions[$name]['value'] = $type->getId();
        }
    }
}
