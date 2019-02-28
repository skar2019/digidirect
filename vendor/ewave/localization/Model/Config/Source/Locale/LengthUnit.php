<?php
namespace Ewave\Localization\Model\Config\Source\Locale;

use Ewave\Localization\Model\UnitsConverter;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class LengthUnit
 * @package Ewave\Localization\Model\Config\Source\Locale
 */
class LengthUnit implements ArrayInterface
{
    /**
     * @var UnitsConverter
     */
    protected $unitsConverter;

    /**
     * LengthUnit constructor.
     * @param UnitsConverter $unitsConverter
     */
    public function __construct(UnitsConverter $unitsConverter)
    {
        $this->unitsConverter = $unitsConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => UnitsConverter::LENGTH_UNIT_MILE, 'label' => __('Miles')]
        ];

        $params = $this->unitsConverter->getUnits();
        foreach ($params as $value => $data) {
            $options[] = ['value' => $value, 'label' => $data['label'] ?? $value];
        }
        return $options;
    }
}
