<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $optionsHash;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
        }

        return $this->options;
    }

    public function toArray(): array
    {
        if ($this->optionsHash === null) {
            $this->optionsHash = $this->collectionFactory->create()->toOptionHash();
        }

        return $this->optionsHash;
    }
}
