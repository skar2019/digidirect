<?php
namespace Ewave\SitemapWidget\Block\Widget\AdditionalEntity;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Element\Template\Context;
use Ewave\SitemapWidget\Model\AdditionalEntity;

/**
 * Class Renderer
 */
class Renderer extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Ewave_SitemapWidget::widget/entity/simple.phtml';

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Renderer constructor.
     * @param Context $context
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param array $entities
     * @return string
     */
    public function render(array $entities)
    {
        $html = '';
        foreach ($entities as $entity) {
            $html .= $this->getItemBlockHtml($entity);
        }
        return $html;
    }

    /**
     * @param array $entity
     * @return string
     */
    public function getItemBlockHtml(array $entity)
    {
        $entityWrap = $this->dataObjectFactory->create();
        $entityWrap->setData($entity);
        $this->setEntity($entityWrap);
        return $this->toHtml();
    }
}
