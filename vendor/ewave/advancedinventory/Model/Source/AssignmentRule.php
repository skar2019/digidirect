<?php
namespace Ewave\AdvancedInventory\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

class AssignmentRule extends AbstractSource implements SourceInterface, OptionSourceInterface, ArrayInterface
{
    /**
     * @var array
     */
    protected $assignmentRules;

    /**
     * AssignmentRule constructor.
     * @param array $assignmentRules
     */
    public function __construct(
        array $assignmentRules = []
    ) {
        $this->assignmentRules = $assignmentRules;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        return $this->assignmentRules;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = $this->getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
