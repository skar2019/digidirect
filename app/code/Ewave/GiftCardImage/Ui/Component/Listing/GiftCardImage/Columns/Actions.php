<?php
namespace Ewave\GiftCardImage\Ui\Component\Listing\GiftCardImage\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    const URL_PATH_DELETE = 'ewave_giftcardimage/index/delete';

    const URL_PATH_EDIT = 'ewave_giftcardimage/index/edit';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['giftcard_image_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, [
                            'giftcard_image_id' => $item['giftcard_image_id']
                        ]),
                        'label' => __('Edit'),
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, [
                            'giftcard_image_id' => $item['giftcard_image_id']
                        ]),
                        'label' => __('Remove'),
                        'confirm' => [
                            'title' => __('Delete the record'),
                            'message' => __('Are you sure you wan\'t to delete the record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
