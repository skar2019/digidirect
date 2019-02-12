<?php
namespace Ewave\Collect\Controller\Adminhtml\Index;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Ewave\Collect\Helper\Config\Import as ImportHelper;
use Ewave\Collect\Model\Config\Backend\Import as ImportModel;

/**
 * Class Import
 * @package Ewave\Class\Controller\Adminhtml\Index
 */
class Import extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ImportHelper
     */
    protected $helper;

    /**
     * @var ImportModel
     */
    protected $modelImport;

    /**
     * Import constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ImportHelper $helper
     * @param ImportModel $model
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ImportHelper $helper,
        ImportModel $model
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->modelImport = $model;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $websiteId = $this->getRequest()->getParam('website_id');
        $countryCode = $this->getRequest()->getParam('country_code');
        $response = [
            'message' =>__('File has been successfully imported'),
            'exported' => true,
            'last_import_time' => null
        ];
        try {
            $scopeInfo = $this->helper->getScopeInfo($storeId, $websiteId);
            $this->modelImport->processImport(
                $this->helper->getAuPostFile($scopeInfo->getScopeId(), $scopeInfo->getScopeType()),
                $countryCode,
                $storeId,
                $websiteId
            );
        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        } catch (Exception $e) {
            $response['exported'] = false;
            $response['message'] = __('Cannot import file. Some error is occurred');
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
