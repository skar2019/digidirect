<?php

namespace Digidirect\Navigation\Ui\Component\Listing\Columns\Set;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;
use Digidirect\Navigation\Model\ResourceModel\Set\CollectionFactory as SetCollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var SetCollectionFactory
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
     * Constructor
     *
     * @param SetCollectionFactory $collectionFactory
     * @param Escaper $escaper
     */
    public function __construct(SetCollectionFactory $collectionFactory, Escaper $escaper)
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
        $collection = $this->collectionFactory->create();
        $this->currentOptions[] = ['label' => '', 'value' => ''];
        foreach ($collection as $type) {
            $name = $this->escaper->escapeHtml($type->getName());
            $this->currentOptions[$name]['label'] = $name;
            $this->currentOptions[$name]['value'] = $type->getId();
        }
    }
}