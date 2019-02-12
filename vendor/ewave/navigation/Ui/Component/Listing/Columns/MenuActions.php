<?php

namespace Ewave\Navigation\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class MenuActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'ewave_navigation/menu/edit';
    const URL_PATH_DELETE = 'ewave_navigation/menu/delete';

    /**
     * @var UrlInterface
     */
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

            $parents = [];
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['parent_menu_item_id']) && isset($item['entity_id'])) {
                    $parents[$item['parent_menu_item_id']] = $item['entity_id'];
                }
            }

            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {

                    $deleteMessage = 'Are you sure you wan\'t to delete a "${ $.$data.title }" record?';
                    if (isset($parents[$item['entity_id']]) && !empty($parents[$item['entity_id']])) {
                        $deleteMessage = 'There are menu items assigned to this parent item.' .
                            ' Are you sure you want to delete this menu item and all the menu items assigned to it?';
                    }
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['entity_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['entity_id'],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.title }"'),
                                'message' => __($deleteMessage),
                            ],
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
