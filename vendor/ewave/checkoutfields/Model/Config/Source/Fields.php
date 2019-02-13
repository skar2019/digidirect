<?php
namespace Ewave\CheckoutFields\Model\Config\Source;

use Ewave\CheckoutFields\Helper\Xml\Fields\Parser;

/**
 * Class Fields
 *
 * @package Ewave\CheckoutFields\Model\Config\Source
 */
class Fields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Ewave\CheckoutFields\Helper\Xml\Fields\Parser
     */
    protected $parser;

    /**
     * @var array
     */
    protected $fields;

    /**
     * Fields constructor.
     *
     * @param \Ewave\CheckoutFields\Helper\Xml\Fields\Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->fields) {
            $fields = $this->parser->getAllFields();
            foreach ($fields as $fieldCode => $fieldData) {
                $this->fields[] = [
                    'value' => $fieldCode,
                    'label' => $fieldData['frontend_name']
                ];
            }
        }

        return $this->fields;
    }
}
