<?php
namespace Ewave\GiftCardImage\Ui\Component\Listing\Columns;

use Ewave\GiftCardImage\Ui\Component\Listing\GiftCardImage\Columns\Actions;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Image
 * @package Ewave\GiftCardImage\Ui\Component\Listing\Columns
 */
class Image extends Column
{
    const NAME = 'thumbnail';
    const ALT_FIELD = 'name';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Ewave\GiftCardImage\Helper\Image
     */
    protected $imageHelper;

    /**
     * Image constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Ewave\GiftCardImage\Helper\Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Ewave\GiftCardImage\Helper\Image $imageHelper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $row = new DataObject($item);
                $imageHelper = $this->imageHelper->init($row, $fieldName);
                $item[$fieldName . '_src'] = $imageHelper->resize(50, 50)->getUrl();
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    Actions::URL_PATH_EDIT,
                    ['giftcard_image_id' => $row->getGiftcardImageId()]
                );
                $item[$fieldName . '_orig_src'] = $imageHelper->getOriginalImageUrl();
            }
        }
        return $dataSource;
    }

    /**
     * Get image alt parameter
     * @param array $row
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
