<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_ITORIS_PRICE_MATCH
 * @copyright  Copyright (c) 2018 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\PriceMatch\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as TypeConfigurable;
use Magento\Customer\Model\Context as ContextCustomer;

class Bootstrap extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $customerSession;
    protected $storeManager;
    protected $productRepository;
    protected $configFactory;
    protected $httpContext;

    public function __construct
    (
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Itoris\PriceMatch\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    )
    {
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->configFactory = $configFactory;
        $this->httpContext = $httpContext;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $storeId   = $this->_request->getParam('sid');
        $productId = $this->_request->getParam('id');
        $customer  = $this->customerSession->getCustomer();

        $product = $this->productRepository->getById($productId,false, $storeId);

        $resultJson = $this->resultJsonFactory->create();
        $response = [
            'product_name' => $product->getName(),
            'final_price' => $product->getFinalPrice(),
            'check_render_link' => ($this->calculateRenderLink()) ? 1 : '',
        ];

        if( $this->customerSession->isLoggedIn() ){
            $response['name'] = $customer->getName();
            $response['email'] = $customer->getEmail();
        }

        return $resultJson->setData($response);
    }

    private function calculateRenderLink()
    {
        $storeId   = $this->_request->getParam('sid');
        $productId = $this->_request->getParam('id');

        $product = $this->productRepository->getById($productId, false, $storeId);
        $typeProduct = $product->getTypeId();
        /** @var \Itoris\PriceMatch\Model\Config $config */
        $config = $this->configFactory->create();
        $configForm = $config->loadItem($product->getId(), $storeId);
        $checkConfig = $this->checkConfigForm($configForm->getGroupList());

        $showLinkConfif = isset($configForm['show_link']) ? true :  false;
        if ($showLinkConfif) {
            $showLink = ($configForm['show_link'] == 0) ? false : true;
        } else {
            $showLink = true;
        }

        return $product->getQuantityAndStockStatus()['is_in_stock'] && $checkConfig && $showLink &&
            ($typeProduct == Type::TYPE_VIRTUAL || $typeProduct == Type::TYPE_SIMPLE || $typeProduct == TypeConfigurable::TYPE_CODE);
    }

    private function checkConfigForm($groupList)
    {
        $customerGroupId = $this->httpContext->getValue(ContextCustomer::CONTEXT_GROUP);

        if (!strlen($groupList)) {
            return false;
        } else {
            if (in_array('-1', explode(',', $groupList))) {
                return true;
            }

            if (!in_array($customerGroupId, explode(',', $groupList))) {
                return false;
            }
        }

        return true;
    }
}
