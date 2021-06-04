<?php

namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone;

/**
 * Class Upload
 *
 * @package Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone
 */
class Upload extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone
{
    const UPLOAD_FORM_FIELD_NAME = 'extendedshippingrates_zone_file';

    /**
     * @var \Ewave\ExtendedShippingRates\Model\Zone\Import
     */
    protected $importProcessor;

    /**
     * @var \Ewave\ExtendedShippingRates\Model\CsvUploader
     */
    protected $csvUploader;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ewave\ExtendedShippingRates\Model\ZoneFactory $zoneFactory
     * @param \Ewave\ExtendedShippingRates\Api\ZoneRepositoryInterface $zoneRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ewave\ExtendedShippingRates\Model\Zone\Import $import
     * @param \Ewave\ExtendedShippingRates\Model\CsvUploader $csvUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ewave\ExtendedShippingRates\Model\ZoneFactory $zoneFactory,
        \Ewave\ExtendedShippingRates\Api\ZoneRepositoryInterface $zoneRepository,
        \Psr\Log\LoggerInterface $logger,
        \Ewave\ExtendedShippingRates\Model\Zone\Import $import,
        \Ewave\ExtendedShippingRates\Model\CsvUploader $csvUploader
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $zoneFactory,
            $zoneRepository,
            $logger
        );
        $this->importProcessor = $import;
        $this->csvUploader = $csvUploader;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $file = $this->getRequest()->getFiles(self::UPLOAD_FORM_FIELD_NAME);
        if ($this->getRequest()->isPost() && !empty($file)) {
            try {
                $result = $this->csvUploader->saveFileToTmpDir($file);
                $this->importProcessor->processImport($result);
                $this->importProcessor->removeCsvFiles($result);
                $this->messageManager->addSuccessMessage(__('The file was uploaded successfully!'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage() . ' The file wasn\'t uploaded.'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }
}
