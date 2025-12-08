<?php
/**
 * Copyright © Digidirect. All rights reserved.
 */

namespace Digidirect\WiserPrice\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Framework\File\Csv;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Digidirect_WiserPrice::wiser_price_import';

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductAction
     */
    protected $productAction;

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param ProductAction $productAction
     * @param Csv $csv
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        ProductAction $productAction,
        Csv $csv,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productAction = $productAction;
        $this->csv = $csv;
        $this->logger = $logger;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        
        try {
            // Check if file was uploaded
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                $this->messageManager->addErrorMessage(__('Please upload a valid CSV file.'));
                return $resultRedirect->setPath('*/*/index');
            }

            $file = $_FILES['csv_file'];
            
            // Validate file extension
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'csv') {
                $this->messageManager->addErrorMessage(__('Please upload a CSV file.'));
                return $resultRedirect->setPath('*/*/index');
            }

            // Parse CSV file
            $csvData = $this->csv->getData($file['tmp_name']);
            
            if (empty($csvData)) {
                $this->messageManager->addErrorMessage(__('The CSV file is empty.'));
                return $resultRedirect->setPath('*/*/index');
            }

            // Get header row
            $headers = array_shift($csvData);
            $headers = array_map('strtolower', array_map('trim', $headers));
            
            // Find column indexes
            $skuIndex = array_search('sku', $headers);
            $priceIndex = array_search('wiser_price', $headers);
            
            if ($skuIndex === false || $priceIndex === false) {
                $this->messageManager->addErrorMessage(
                    __('CSV file must contain "sku" and "wiser_price" columns.')
                );
                return $resultRedirect->setPath('*/*/index');
            }

            // Prepare data for batch processing
            $skuPriceMap = [];
            $skippedCount = 0;

            // Process each row and build SKU to price map
            foreach ($csvData as $rowIndex => $row) {
                if (!isset($row[$skuIndex]) || !isset($row[$priceIndex])) {
                    $skippedCount++;
                    continue;
                }
                
                $sku = trim($row[$skuIndex]);
                $wiserPrice = trim($row[$priceIndex]);
                
                if (empty($sku)) {
                    $skippedCount++;
                    continue;
                }

                $skuPriceMap[$sku] = $wiserPrice;
            }

            if (empty($skuPriceMap)) {
                $this->messageManager->addErrorMessage(__('No valid data found in CSV file.'));
                return $resultRedirect->setPath('*/*/index');
            }

            // Get product IDs for all SKUs
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('entity_id')
                      ->addAttributeToFilter('sku', ['in' => array_keys($skuPriceMap)]);

            $productIdMap = [];
            $foundSkus = [];
            
            foreach ($collection as $product) {
                $sku = $product->getSku();
                $productIdMap[$product->getId()] = $skuPriceMap[$sku];
                $foundSkus[] = $sku;
            }

            $updatedCount = 0;
            $errorCount = 0;
            $notFoundSkus = array_diff(array_keys($skuPriceMap), $foundSkus);

            // Batch update products using Product Action
            if (!empty($productIdMap)) {
                try {
                    foreach ($productIdMap as $productId => $wiserPrice) {
                        try {
                            $this->productAction->updateAttributes(
                                [$productId],
                                ['wiser_price' => $wiserPrice],
                                Store::DEFAULT_STORE_ID
                            );
                            $updatedCount++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $this->logger->error('Wiser Price Import Error for Product ID ' . $productId . ': ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('An error occurred during batch update: %1', $e->getMessage())
                    );
                    $this->logger->critical('Wiser Price Import Batch Update Error: ' . $e->getMessage());
                }
            }

            // Add success message
            if ($updatedCount > 0) {
                $this->messageManager->addSuccessMessage(
                    __('Successfully updated %1 product(s).', $updatedCount)
                );
            }
            
            // Add warning for skipped rows
            if ($skippedCount > 0) {
                $this->messageManager->addWarningMessage(
                    __('Skipped %1 row(s) due to missing data.', $skippedCount)
                );
            }
            
            // Add error messages for products not found
            if (!empty($notFoundSkus)) {
                $errorCount += count($notFoundSkus);
                $this->messageManager->addErrorMessage(
                    __('Failed to find %1 product(s) by SKU.', count($notFoundSkus))
                );
                
                // Show first 5 missing SKUs
                $displaySkus = array_slice($notFoundSkus, 0, 5);
                foreach ($displaySkus as $sku) {
                    $this->messageManager->addErrorMessage(__('Product not found: %1', $sku));
                    $this->logger->warning('Wiser Price Import: Product not found - SKU: ' . $sku);
                }
                
                if (count($notFoundSkus) > 5) {
                    $this->messageManager->addNoticeMessage(
                        __('And %1 more SKU(s) not found. Check system.log for full details.', count($notFoundSkus) - 5)
                    );
                }
            }
            
            // Add error count if any
            if ($errorCount > 0 && $errorCount > count($notFoundSkus)) {
                $updateErrors = $errorCount - count($notFoundSkus);
                $this->messageManager->addErrorMessage(
                    __('Failed to update %1 product(s). Check logs for details.', $updateErrors)
                );
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing the file: %1', $e->getMessage())
            );
            $this->logger->critical('Wiser Price Import Critical Error: ' . $e->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
