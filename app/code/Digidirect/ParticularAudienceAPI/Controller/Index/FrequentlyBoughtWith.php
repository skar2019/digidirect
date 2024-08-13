<?php

namespace Digidirect\ParticularAudienceAPI\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class FrequentlyBoughtWith extends Action {
    
    private $checkoutSession;

    private $cartRepository;

    private $productRepository;

    private $json;

    private $configurableType;

    protected $logger;

    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->configurableType = $configurableType;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
    * @return ResultInterface
    * @throws LocalizedException
    */
    public function execute()
    {
        $productIds = $this->getRequest()->getParam('productIds');

        $session = $this->checkoutSession->create();
        $quote = $session->getQuote();

        foreach($productIds as $item) {
            $this->logger->info("item: " . $item);
            $product = $this->productRepository->getById($item);
            $quote->addProduct($product, 1);
        }

        $this->cartRepository->save($quote);
        $session->replaceQuote($quote)->unsLastRealOrderId();

        return true;
    }
    }
