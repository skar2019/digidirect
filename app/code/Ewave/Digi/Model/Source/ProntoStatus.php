<?php
namespace Ewave\Digi\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AbstractEntity\Model\AbstractEntity;

/**
 * Class ProntoStatus
 * @package Ewave\Digi\Model\Source
 */
class ProntoStatus extends AbstractSource implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            [
                'value' => '',
                'label' => '-- Choose an option --',
            ],
            [
                'value' => 'Empty',
                'label' => 'Empty',
            ],
            [
                'value' => 'S',
                'label' => 'S',
            ],
            [
                'value' => 'K',
                'label' => 'K',
            ],
            [
                'value' => 'I',
                'label' => 'I',
            ],
            [
                'value' => 'Z',
                'label' => 'Z',
            ],
            [
                'value' => 'O',
                'label' => 'O',
            ]
        ];

        return $options;
    }
}
