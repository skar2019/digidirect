<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Category\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;

class Mapping extends AbstractElement
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Mapping constructor.
     * @param Factory $factory
     * @param CollectionFactory $collectionFactory
     * @param Escaper $escaper
     * @param Registry $registry
     * @param LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        Factory $factory,
        CollectionFactory $collectionFactory,
        Escaper $escaper,
        Registry $registry,
        LayoutInterface $layout,
        array $data = []
    ) {
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
            ->setTemplate('Ewave_Feed::dynamic/category/edit/form.phtml')
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
                        'dynamic_category' => [
                            'component' => 'Ewave_Feed/js/dynamic/category',
                            'config' => [
                                'mapping' => array_values($this->getCategory()->getMapping()),
                            ]
                        ],
                        'dynamic_category_search' => [
                            'component' => 'Ewave_Feed/js/dynamic/category/search',
                            'config' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return \Ewave\Feed\Model\Dynamic\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_model');
    }
}
