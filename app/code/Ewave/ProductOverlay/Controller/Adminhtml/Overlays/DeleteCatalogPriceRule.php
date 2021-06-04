<?php
namespace Ewave\ProductOverlay\Controller\Adminhtml\Overlays;

use Ewave\ProductOverlay\Helper\Timezone;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class DeleteCatalogPriceRule
 * @package Ewave\ProductOverlay\Controller\Adminhtml\Overlays
 */
class DeleteCatalogPriceRule extends \Ewave\ProductOverlay\Controller\Adminhtml\Overlays
{
    /**
     * @var \Ewave\ProductOverlay\Model\Rule\CatalogRuleOverlays
     */
    protected $catalogRuleOverlays;

    /**
     * DeleteCatalogPriceRule constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Ewave\ProductOverlay\Model\OverlaysFactory $overlayFactory
     * @param \Ewave\ProductOverlay\Helper\Data $overlayHelper
     * @param \Ewave\ProductOverlay\Model\RuleFactory $overlayRuleFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Ewave\ProductOverlay\Model\OverlaysRepository $overlayRepository
     * @param \Ewave\ProductOverlay\Model\ResourceModel\Overlays\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Ewave\ProductOverlay\Model\Rule\CatalogRuleOverlays $catalogRuleOverlays
     * @param array $data
     * @param Timezone|null $timezone
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Date $dateFilter,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Ewave\ProductOverlay\Model\OverlaysFactory $overlayFactory,
        \Ewave\ProductOverlay\Helper\Data $overlayHelper,
        \Ewave\ProductOverlay\Model\RuleFactory $overlayRuleFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $file,
        \Ewave\ProductOverlay\Model\OverlaysRepository $overlayRepository,
        \Ewave\ProductOverlay\Model\ResourceModel\Overlays\CollectionFactory $collectionFactory,
        Filter $filter,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Ewave\ProductOverlay\Model\Rule\CatalogRuleOverlays $catalogRuleOverlays,
        array $data = [],
        Timezone $timezone = null
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $resultForwardFactory,
            $resultPageFactory,
            $dateFilter,
            $filesystem,
            $fileUploaderFactory,
            $overlayFactory,
            $overlayHelper,
            $overlayRuleFactory,
            $logger,
            $file,
            $overlayRepository,
            $collectionFactory,
            $filter,
            $typeList,
            $serializer,
            $data,
            $timezone
        );
        $this->catalogRuleOverlays = $catalogRuleOverlays;
    }

    /**
     * Overlay Delete Action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * @var \Magento\Framework\Message\ManagerInterface $messageManager
         */
        $messageManager = $this->getMessageManager();
        $overlayId = (int)$this->getRequest()->getParam('overlay_id');
        $ruleId = (int)$this->getRequest()->getParam('row_id');

        if ($overlayId && $ruleId) {
            try {
                $this->catalogRuleOverlays->deleteRelation($overlayId, $ruleId);
                $messageManager->addSuccessMessage(__('You deleted the catalog rule relation.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $messageManager->addErrorMessage(__('We can\'t delete item right now.'));
                $this->_logger->critical($e);
            }
        } else {
            $messageManager->addErrorMessage(__('We can\'t find an item to delete.'));
        }

        if ($overlayId) {
            $this->_redirect('ewave_productoverlay/*/edit', ['id' => $overlayId]);
        } else {
            $this->_redirect('ewave_productoverlay/*/');
        }
    }
}
