<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Controller\Adminhtml\Wysiwyg\Images;

class OnInsert extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magezon\Core\Helper\Wysiwyg\Images
     */
    private $imagesHelper;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $catalogHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magezon\Core\Helper\Wysiwyg\Images $imagesHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magezon\Core\Helper\Wysiwyg\Images $imagesHelper,
        \Magento\Catalog\Helper\Data $catalogHelper
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->imagesHelper = $imagesHelper;
        $this->catalogHelper = $catalogHelper;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Fire when select image
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $this->imagesHelper->idDecode($filename);
        $asIs = $this->getRequest()->getParam('as_is');

        $this->catalogHelper->setStoreId($storeId);
        $this->imagesHelper->setStoreId($storeId);

        $image = $this->imagesHelper->getImageHtmlDeclaration($filename, $asIs);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($image);
    }
}
