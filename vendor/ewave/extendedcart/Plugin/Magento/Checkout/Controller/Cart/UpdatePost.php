<?php

namespace Ewave\ExtendedCart\Plugin\Magento\Checkout\Controller\Cart;

use Magento\Framework\View\Element\BlockInterface;

class UpdatePost
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializerJson;

    /**
     * @var \Ewave\ExtendedCart\Helper\CartPage
     */
    protected $cartPageHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * UpdatePost constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serializerJson
     * @param \Ewave\ExtendedCart\Helper\CartPage $cartPageHelper
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializerJson,
        \Ewave\ExtendedCart\Helper\CartPage $cartPageHelper,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->context = $context;
        $this->serializerJson = $serializerJson;
        $this->cartPageHelper = $cartPageHelper;
        $this->cart = $cart;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\UpdatePost $controllerAction
     * @param \Closure $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\UpdatePost $controllerAction,
        \Closure $proceed
    ) {
        /**
         * @var $request \Magento\Framework\App\Request\Http
         * @var $response \Magento\Framework\App\Response\Http
         * @var $result \Magento\Framework\Controller\Result\Redirect
         * @var $view \Magento\Framework\App\View
         */
        $result = $proceed();
        $request = $controllerAction->getRequest();
        $response = $controllerAction->getResponse();
        if (!$request->isAjax() || !$this->cartPageHelper->isAjaxUpdateEnabled()) {
            return $result;
        }

        $reload = !$this->cart->getQuote()->hasItems();
        $responseData = [
            'reload' => $reload,
        ];

        if (!$reload) {
            $view = $this->context->getView();
            $view->loadLayout(['default', 'checkout_cart_index', $view->getDefaultLayoutHandle()], true, true, false);
            $layout = $view->getLayout();

            $responseDataBlocks = [];
            $ajaxUpdateBlock = $layout->getBlock('ewave_extendedcart.ajax_update');
            if (!$ajaxUpdateBlock instanceof BlockInterface) {
                return $result;
            }

            $blockNames = $ajaxUpdateBlock->getAjaxUpdateBlockNames();
            foreach ($blockNames as $jsonKey => $blockName) {
                $block = $layout->getBlock($blockName);
                if ($block instanceof BlockInterface) {
                    $responseDataBlocks[$jsonKey] = $block->toHtml();
                }
            }
            $responseData['blocks'] = $responseDataBlocks;
        }

        $responseData['reload'] = $reload;
        return $response->representJson(
            $this->serializerJson->serialize($responseData)
        );
    }
}
