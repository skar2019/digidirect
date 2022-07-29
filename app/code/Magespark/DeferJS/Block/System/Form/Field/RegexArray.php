<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark_CopyCmsPageBlock
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */
namespace MageSpark\DeferJS\Block\System\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;

class RegexArray extends AbstractFieldArray
{
    /**
     * @var Factory
     */
    public $elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {

        $this->elementFactory  = $elementFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->addColumn(
            'deferjs',
            [
                'label' => __('Matched Expression'),
                'class' => 'required-entry'
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Match');
        parent::_construct();
    }
}
