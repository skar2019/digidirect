<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Adminhtml\Slider\Widget;

use Amasty\BannerSlider\Model\Repository\SliderRepository;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Chooser extends Extended
{
    /**
     * @var \Amasty\BannerSlider\Model\ResourceModel\Slider\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var SliderRepository
     */
    private $sliderRepository;

    /**
     * @var \Amasty\BannerSlider\Model\OptionSource\Status
     */
    private $status;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Amasty\BannerSlider\Model\ResourceModel\Slider\Grid\CollectionFactory $collectionFactory,
        SliderRepository $sliderRepository,
        \Amasty\BannerSlider\Model\OptionSource\Status $status,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->sliderRepository = $sliderRepository;
        $this->status = $status;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'ambannerslider/slider_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false]
        );

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        $value = (int) $element->getValue();
        if ($value && $this->sliderRepository->isSliderExist($value)) {
            $chooser->setLabel($this->sliderRepository->getById($value)->getName());
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        return '
            function (grid, event) {
            var trElement = Event.findElement(event, "tr");
            var sliderId = trElement.down("td").innerHTML;
            var sliderName = trElement.down("td").next().innerHTML;
            var optionLabel = sliderName;
            var optionValue = sliderId.replace(/^\s+|\s+$/g,"");
            ' .
            $chooserJsObject .
            '.setElementValue(optionValue);
                ' .
            $chooserJsObject .
            '.setElementLabel(optionLabel);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Slider Name'),
                'name' => 'name',
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'name' => 'status',
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->toArray(),
                'renderer' => \Amasty\BannerSlider\Block\Adminhtml\Slider\Widget\Columns\Status::class,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        $this->addColumn(
            'banner_names',
            [
                'header' => __('Banners'),
                'name' => 'banner_names',
                'index' => 'banner_names',
                'renderer' => \Amasty\BannerSlider\Block\Adminhtml\Slider\Widget\Columns\Banners::class,
                'header_css_class' => 'col-banner',
                'column_css_class' => 'col-banner'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'ambannerslider/slider_widget/chooser',
            [
                '_current' => true,
                'uniq_id' => $this->getId()
            ]
        );
    }
}
