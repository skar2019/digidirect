<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Controller\Adminhtml\Rule;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\ProductLabels\Controller\Adminhtml\Rule;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Helper\Image;
use Mageplaza\ProductLabels\Model\Indexer\RuleIndexer;
use Mageplaza\ProductLabels\Model\RuleFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\ProductLabels\Controller\Adminhtml\Rule
 */
class Save extends Rule
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var RuleIndexer
     */
    protected $ruleIndexer;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param Js $jsHelper
     * @param DateTime $date
     * @param Image $imageHelper
     * @param RuleIndexer $ruleIndexer
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        Registry $coreRegistry,
        HelperData $helperData,
        Js $jsHelper,
        DateTime $date,
        Image $imageHelper,
        RuleIndexer $ruleIndexer
    ) {
        $this->jsHelper    = $jsHelper;
        $this->date        = $date;
        $this->imageHelper = $imageHelper;

        parent::__construct($context, $ruleFactory, $coreRegistry, $helperData, $ruleIndexer);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('rule')) {
            /** @var \Mageplaza\ProductLabels\Model\Rule $rule */

            $rule = $this->initRule();

            try {
                /** get rule conditions */
                $rule->loadPost($data);
                $this->prepareData($rule, $data);
                $this->_eventManager->dispatch('mageplaza_productlabels_rule_prepare_save', [
                    'post'    => $rule,
                    'request' => $this->getRequest()
                ]);

                $rule->save();
                $this->_getSession()->setData('mageplaza_productlabels_rule_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpproductlabels/*/edit', ['id' => $rule->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }

                $type = $this->getRequest()->getParam('type');
                if (!empty($type) && $type === 'saveAndApply') {
                    $this->ruleIndexer->executeFull();
                    $this->messageManager->addSuccessMessage(__('The Rule has been saved and applied.'));
                } else {
                    $this->messageManager->addSuccessMessage(__('The Rule has been saved.'));
                }

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the Rule: %1', $e->getMessage())
                );
            }

            $resultRedirect->setPath('mpproductlabels/*/edit', ['id' => $rule->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpproductlabels/*/');

        return $resultRedirect;
    }

    /**
     * @param $rule
     * @param array $data
     *
     * @return $this
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function prepareData($rule, $data = [])
    {
        if (isset($data['to_date']) && !empty($data['to_date'])
            && strtotime($data['to_date']) < strtotime($data['from_date'])
        ) {
            throw new LocalizedException(__('To date should be after From date'));
        }

        $this->imageHelper->uploadImage(
            $data,
            'label_image',
            Image::TEMPLATE_MEDIA_PRODUCT_LABEL,
            $rule->getLabelImage()
        );
        $this->imageHelper->uploadImage(
            $data,
            'list_image',
            Image::TEMPLATE_MEDIA_LISTING_LABEL,
            $rule->getListImage()
        );

        if ($rule->getCreatedAt() === null) {
            $data['created_at'] = $this->date->date();
        }

        if ((isset($data['from_date']) && empty($data['from_date'])) || $rule->getFromDate() === null) {
            $data['from_date'] = $this->date->date();
        }

        $data['updated_at'] = $this->date->date();

        if ($data['same']) {
            $data['list_template']      = $data['label_template'];
            $data['list_image']         = $data['label_image'];
            $data['list_label']         = $data['label'];
            $data['list_font']          = $data['label_font'];
            $data['list_font_size']     = $data['label_font_size'];
            $data['list_color']         = $data['label_color'];
            $data['list_css']           = $data['label_css'];
            $data['list_position']      = $data['label_position'];
            $data['list_position_grid'] = $data['label_position_grid'];
        }

        if (!empty($data['product_tooltip'])) {
            $data['product_tooltip'] = HelperData::jsonEncode($data['product_tooltip']);
        }

        if (!empty($data['list_product_tooltip'])) {
            $data['list_product_tooltip'] = HelperData::jsonEncode($data['list_product_tooltip']);
        }

        $rule->addData($data);

        return $this;
    }
}
