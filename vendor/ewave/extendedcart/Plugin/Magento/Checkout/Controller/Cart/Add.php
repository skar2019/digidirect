<?php

namespace Ewave\ExtendedCart\Plugin\Magento\Checkout\Controller\Cart;

class Add
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializerJson;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlDataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $serializerJson
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Url\Helper\Data $urlDataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $serializerJson,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Url\Helper\Data $urlDataHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->serializerJson = $serializerJson;
        $this->productMetadata = $productMetadata;
        $this->urlDataHelper = $urlDataHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return $this
     */
    protected function initProduct()
    {
        $productId = (int)$this->context->getRequest()->getParam('product');
        $storeId = $this->storeManager->getStore()->getId();
        $product = $this->productRepository->getById($productId, false, $storeId);
        $this->registry->register('current_product', $product);
        $this->registry->register('product', $product);
        return $this;
    }

    /**
     * @param string $html
     * @return string
     */
    protected function replaceUrlEncoded($html)
    {
        /**
         * @var $request \Magento\Framework\App\Request\Http
         */
        $request = $this->context->getRequest();

        $paramNameUrlEncoded = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;

        $neededUrlEncoded = $request->getParam($paramNameUrlEncoded);
        $currentUrlEncoded = $this->urlDataHelper->getEncodedUrl();

        $pattern = '/%s/%s/';
        $search = sprintf($pattern, $paramNameUrlEncoded, rawurlencode($currentUrlEncoded));
        $replace = sprintf($pattern, $paramNameUrlEncoded, rawurlencode($neededUrlEncoded));
        $html = str_replace($search, $replace, $html);

        return $html;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Add $controllerAction
     * @param \Closure $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\Add $controllerAction,
        \Closure $proceed
    ) {
        /**
         * @var $quote \Magento\Quote\Model\Quote
         * @var $view \Magento\Framework\App\View
         * @var $request \Magento\Framework\App\Request\Http
         * @var $response \Magento\Framework\App\Response\Http
         */
        $result = $proceed();
        $request = $this->context->getRequest();
        if (!$request->isAjax()) {
            return $result;
        }

        $quote = $this->checkoutSession->getQuote();
        if ($quote->getHasError()) {
            return $result;
        }

        $this->initProduct();

        $response = $this->context->getResponse();
        $body = $response->getBody();
        $body = $this->serializerJson->unserialize($body);
        $response->clearBody();

        $view = $this->context->getView();
        $view->addActionLayoutHandles();

        if ($this->productMetadata->getEdition() != \Magento\Framework\App\ProductMetadata::EDITION_NAME) {
            $editionHandle = $view->getDefaultLayoutHandle() . '_' . strtolower($this->productMetadata->getEdition());
            $view->getLayout()->getUpdate()->addHandle($editionHandle);
        }

        $view->loadLayout(null, true, true, false);
        $view->renderLayout();

        $popupHtml = $response->getBody();
        $popupHtml = $this->replaceUrlEncoded($popupHtml);
        $body['confirmation_popup'] = $popupHtml;

        $response->representJson(
            $this->serializerJson->serialize($body)
        );

        return $result;
    }
}
