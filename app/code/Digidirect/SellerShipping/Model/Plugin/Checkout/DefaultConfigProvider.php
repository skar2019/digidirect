<?php

namespace Digidirect\SellerShipping\Model\Plugin\Checkout;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider
{
    
    protected $helperData;
    
    protected $logger;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    private $quoteItemFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    
    
    public function __construct(
        \Digidirect\SellerShipping\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * AfterGetConfig
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param [] $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        //$this->logger->info('DefaultConfigProvider getSellersShipping(): ' . $this->helperData->getSellerShipping());
        $result['quoteData']['has_marketplacer_seller'] = $this->helperData->hasMarketplacerSeller();
        $result['quoteData']['marketplacer_sellers'] = $this->helperData->getSellers();
        
        $items = $result['totalsData']['items'];

        for($i=0; $i < count($items); $i++){
            $quoteId = $items[$i]['item_id'];
            $quote = $this->quoteItemFactory->create()->load($quoteId);
            $productId = $quote->getProductId();
            $product = $this->productRepository->getById($productId);
            $productSeller = $product->getResource()->getAttribute('marketplacer_seller')->getFrontend()->getValue($product);       
            $items[$i]['marketplacer_seller'] = $productSeller;
        }
        $result['totalsData']['items'] = $items;
        
        return $result;
    }

}
