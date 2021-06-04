<?php

namespace Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab;

class ProductChooser extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChooserHtml();
    }

    /**
     * @return string
     */
    protected function getChooserHtml()
    {
        $uniqId = 'product_chooser';
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $productTypeId = $this->getRequest()->getParam('product_type_id', null);

        $productsGrid = $this->_layout->createBlock(
            \Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser::class,
            '',
            [
                'data' => [
                    'id' => $uniqId,
                    'use_massaction' => $massAction,
                    'product_type_id' => $productTypeId,
                    'category_id' => $this->getRequest()->getParam('category_id'),
                ]
            ]
        );

        $html = $productsGrid->toHtml();

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $this->_layout->createBlock(
                \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser::class,
                '',
                [
                    'data' => [
                        'id' => $uniqId . 'Tree',
                        'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                        'with_empty_node' => true,
                    ]
                ]
            );

            $html = $this->_layout->createBlock(\Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser\Container::class)
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }
        return $html;
    }
}