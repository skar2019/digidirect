<?php

namespace Ewave\AI\Model\Config\Source;

use Ewave\AI\Model\Integrations\Integrations;
use Ewave\AI\Model\ResourceModel\Integrations\Integrations\Collection;
use Ewave\AI\Model\ResourceModel\Integrations\Integrations\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Integration implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Integration constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /**
             * @var $collection Collection
             * @var $item Integrations
             */
            $options = [];
            $collection = $this->collectionFactory->create();
            foreach ($collection as $item) {
                $options[] = [
                    'label' => $item->getIntegrationName(),
                    'value' => $item->getProcessCode(),
                ];
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
