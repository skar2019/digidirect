<?php

namespace Digidirect\SellerShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{
    protected $session;
    protected $logger;
    protected $productFactory;
    protected $quoteRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Session $session,
        LoggerInterface $logger,
        ProductFactory $productFactory,
        CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Get seller shipping total
     *
     * @return float
     */
    public function getSellerShipping()
    {
        $quoteId = $this->session->getQuoteId();
        $quote   = $this->quoteRepository->get($quoteId);
        $items   = $quote->getAllItems();

        $sellers = [];
        $zeroShipping = ["LPX Trading Pty Ltd","LatestBuy","Wilson Trading Import Pty Ltd","eMega"];

        foreach ($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            $seller  = $product->getAttributeText('marketplacer_seller');

            if ($seller == "") {
                $seller = "digiDirect";
            }

            if (!in_array($seller, $sellers)) {
                $sellers[] = $seller;
            }
        }

        $sellerTotalShipping = 0;
        $digidirectSeller = 0;
        $nonDigidirectSeller = 0;
        $digidirectSellerCount = 0;
        $nonDigidirectSellerCount = 0;
        $standardShipping = 8.95;

        foreach ($sellers as $seller) {
            $sellerTotal = 0;

            foreach ($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                $finalPrice = $product->getFinalPrice();
                $productTotal = $finalPrice * $item->getQty();
                $itemSeller = $product->getAttributeText('marketplacer_seller');

                if ($seller == $itemSeller) {
                    $sellerTotal += $productTotal;
                }
            }
            
            //Free Shipping $99
            if ($seller == "digiDirect") {
                if ($sellerTotal > 98) {
                    $standardShipping = 0;
                }
                $digidirectSellerCount++;
            } elseif (in_array($seller, $zeroShipping)) {
                $nonDigidirectSellerCount++;
            } else {
                $nonDigidirectSeller += $standardShipping;
                $nonDigidirectSellerCount++;
            }
        }

        if ($nonDigidirectSellerCount > 0 && $digidirectSellerCount == 0) {
            $nonDigidirectSeller -= $standardShipping;
        }

        $sellerTotalShipping = $nonDigidirectSeller;
        return $sellerTotalShipping;
    }

    public function getSellers()
    {
        $quoteId = $this->session->getQuoteId();
        $quote   = $this->quoteRepository->get($quoteId);
        $items   = $quote->getAllItems();

        $sellers = [];
        $zeroShipping = ["LPX Trading Pty Ltd","LatestBuy","Wilson Trading Import Pty Ltd","eMega"];

        foreach ($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            $seller  = $product->getAttributeText('marketplacer_seller');

            if ($seller == "") {
                $seller = "digiDirect";
            }

            if (!in_array($seller, $sellers)) {
                $sellers[] = $seller;
            }
        }

        $sellersArray = [];

        foreach ($sellers as $seller) {
            $sellerTotal = 0;

            foreach ($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                $finalPrice = $product->getFinalPrice();
                $productTotal = $finalPrice * $item->getQty();
                $itemSeller = $product->getAttributeText('marketplacer_seller');

                if ($seller == $itemSeller) {
                    $sellerTotal += $productTotal;
                }
            }

            //Free Shipping $99
            $sellerShipping = 8.95;
            if ($seller == "digiDirect" && $sellerTotal > 98) {
                $sellerShipping = 0;
            }
            //
            
            if (in_array($seller, $zeroShipping)) {
                $sellerShipping = 0;
            }

            if (!in_array($seller, $sellersArray)) {
                $sellersArray[] = [$seller, $sellerShipping];
            }
        }

        return $sellersArray;
    }

    public function hasMarketplacerSeller()
    {
        $sellers = $this->getSellers();
        $thirdPartyCount = 0;

        foreach ($sellers as $seller) {
            if ($seller[0] != "digiDirect") {
                $thirdPartyCount++;
            }
        }

        return $thirdPartyCount > 0;
    }

    public function checkForBulkyItems()
    {
        $quoteId = $this->session->getQuoteId();
        $quote   = $this->quoteRepository->get($quoteId);
        $items   = $quote->getAllItems();

        $ctr = 0;
        foreach ($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            $isBulky = $product->getData('bulky_item');
            if ($isBulky == 1) {
                $ctr++;
            }
        }

        return $ctr > 0;
    }

    public function getDigiShipping()
    {
        $sellers = $this->getSellers();
        $standardShipping = 8.95;
        
        foreach($sellers as $seller){
            $this->logger->info('getDigiShipping: ' . $seller[0] . ", " .$seller[1]);
            if ($seller[0] == "digiDirect" && $seller[1] == 0) {
                $standardShipping = 0;
            }
        }
        
        $bulkItemSurcharge = 0;
        if ($this->checkForBulkyItems() == true) {
            $bulkItemSurcharge = 20;
        }
        return $standardShipping + $bulkItemSurcharge;
    }
}
