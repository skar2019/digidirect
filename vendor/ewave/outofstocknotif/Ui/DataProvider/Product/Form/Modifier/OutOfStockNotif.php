<?php

namespace Ewave\OutOfStockNotif\Ui\DataProvider\Product\Form\Modifier;

use Ewave\OutOfStockNotif\Setup\UpgradeData;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class OutOfStockNotif
 */
class OutOfStockNotif extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * OutOfStockNotif constructor.
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $weightPath = $this->arrayManager->findPath(
            UpgradeData::ATTRIBUTE_NAME_AVAILABEL_DATE,
            $meta,
            null,
            'children'
        );

        if ($weightPath) {
            $meta = $this->arrayManager->merge(
                $weightPath . static::META_CONFIG_PATH,
                $meta,
                [
                    'validation' => [
                        'required-entry' => true
                    ],
                    'imports' => [
                        'disabled' => '!ns = ${ $.ns }, index = ' .
                            UpgradeData::ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE . ':checked'
                    ],
                    'options' => [
                        'showsTime' => true
                    ]
                ]
            );
        }
        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
