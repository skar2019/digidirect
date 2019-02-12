<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Attribute\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;

class Conditions extends AbstractElement
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Fieldset
     */
    protected $fieldset;

    /**
     * Conditions constructor.
     * @param Factory $factory
     * @param CollectionFactory $collectionFactory
     * @param Escaper $escaper
     * @param Fieldset $fieldset
     * @param Registry $registry
     * @param LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        Factory $factory,
        CollectionFactory $collectionFactory,
        Escaper $escaper,
        Fieldset $fieldset,
        Registry $registry,
        LayoutInterface $layout,
        array $data = []
    ) {
        $this->fieldset = $fieldset;
        $this->registry = $registry;
        $this->layout = $layout;

        parent::__construct($factory, $collectionFactory, $escaper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        return $this->layout
            ->createBlock('Magento\Backend\Block\Template')
            ->setData('js_config', $this->getJsConfig())
            ->setData('parent', $this)
            ->setTemplate('Ewave_Feed::dynamic/attribute/edit/form.phtml')
            ->toHtml();
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'dynamic_attribute' => [
                            'component' => 'Ewave_Feed/js/dynamic/attribute',
                            'config' => [
                                'conditions' => $this->getAttribute()->getConditions()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return \Ewave\Feed\Model\Dynamic\Attribute
     */
    public function getAttribute()
    {
        return $this->registry->registry('current_model');
    }
}
