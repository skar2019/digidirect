<?php

namespace Ewave\ProntoDigi\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ewave\CollectAbstractEntity\Model\CollectPlaceRepository;

class CollectPlace implements OptionSourceInterface
{
    /**
     * @var CollectPlaceRepository
     */
    protected $collectPlaceRepository;

    /**
     * CollectPlace constructor.
     * @param CollectPlaceRepository $collectPlaceRepository
     */
    public function __construct(
        CollectPlaceRepository $collectPlaceRepository
    ) {
        $this->collectPlaceRepository = $collectPlaceRepository;
    }

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $options = [];

        if (!$isMultiselect) {
            $options = [
                'label' => '',
                'value' => '',
            ];
        }
        $items = $this->collectPlaceRepository->getAll();
        foreach ($items as $item) {
            $options[] = [
                'label' => $item->getName(),
                'value' => $item->getId(),
            ];
        }

        return $options;
    }
}
