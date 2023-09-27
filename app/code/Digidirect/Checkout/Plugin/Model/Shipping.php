<?php

namespace Digidirect\Checkout\Plugin\Model;

use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class Shipping {
    
    protected $logger;
    
    public function __construct(
        GetSourceItemsBySku $getSourceItemsBySku,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->logger = $logger;
    }
       
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        $items = $cart->getQuote()->getAllItems();
        $postCode = $cart->getQuote()->getShippingAddress()->getPostcode();
        
        $swhsPostCodes = [2044,2043,2042,2015,2204,2050,2049,2048,2017,2016,2205,2008,2038,2037,2040,2007,2000,2018,2203,2033,2020,2130,2010,2021,2216,2193,2206,2009,2039,2207,2131,2045,2032,2019,2031,2041,2132,2011,2022,2046,2136,2036,2035,2047,2024,2025,2133,2208,2034,2027,2218,2217,2134,2194,2196,2028,2192,2026,2061,2060,2137,2111,2023,2110,2195,2219,2191,2220,2221,2135,2089,2209,2066,2140,2029,2062,2065,2090,2030,2138,2088,2139,2063,2068,2190,2222,2112,2141,2229,2113,2127,2210,2224,2064,2223,2144,2143,2067,2128,2114,2199,2142,2211,2228,2200,2093,2069,2227,2212,2070,2092,2230,2232,2122,2094,2197,2115,2150,2213,2162,2100,2116,2226,2214,2117,2225,2071,2087,2095,2145,2096,2234,2151,2163,2072,2086,2161,2160,2118,2198,2099,2109,2164,2231,2085,2233,2173,2170,2075,2073,2097,2152,2146,2119,2153,2165,2121,2074,2172,2166,2148];
        $melbPostCodes = [3053,3054,3008,3002,3031,3031,3000,3052,3207,3006,3141,3003,3206,3183,3184,3206,3185,3182,3183,3182,3067,3078,3121,3068,3066,3121,3078,3065,3068,3054,3121,3081,3088,3083,3084,3095,3088,3084,3081,3081,3079,3079,3093,3094,3084,3084,3087,3087,3085,3083,3070,3072,3073,3071,3049,3047,3061,3048,3064,3047,3043,3059,3047,3036,3048,3045,3064,3062,3043,3049,3428,3063,3063,3040,3042,3032,3034,3040,3041,3040,3033,3039,3042,3041,3041,3032,3056,3057,3055,3058,3058,3060,3046,3043,3046,3046,3044,3044,3089,3095,3095,3090,3091,3076,3075,3082,3074,3147,3103,3124,3126,3146,3122,3123,3101,3102,3127,3129,3127,3179,3105,3108,3109,3114,3106,3107,3113,3134,3134,3130,3130,3130,3128,3129,3128,3125,3151,3131,3132,3131,3133,3193,3193,3186,3187,3192,3185,3188,3190,3191,3174,3174,3171,3204,3165,3162,3145,3161,3162,3163,3185,3163,3204,3163,3204,3195,3195,3169,3169,3172,3196,3202,3194,3189,3194,3195,3195,3147,3148,3168,3150,3166,3166,3149,3170,3168,3166,3166,3167,3150,3143,3144,3144,3145,3181,3142,3181,3021,3020,3022,3012,3023,3049,3023,3037,3021,3038,3038,3036,3042,3021,3021,3020,3020,3037,3038,3018,3028,3025,3028,3015,3015,3028,3018,3015,3016,3016,3019,3011,3012,3012,3032,3011,3012,3012,3013,3023,3023,3023,3023,3029,3029,3026,3030,3006,3103,3111,3027,3205,3188,3020,3104,3041,3088,3085,3058,3051,3026,3148,3170,3032];
        
        $isSwhs = 0;
        $isMelb = 0;
        
        if (in_array($postCode, $swhsPostCodes)) {
            $isSwhs = 1;
        }
        if (in_array($postCode, $melbPostCodes)) {
            $isMelb = 1;
        }
        
        $swhsQty = 1;
        $melbQty = 1;
        
        foreach ($items as $item) {

            $prodId = $item->getProductId();
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $_objectManager->get('\Magento\Catalog\Model\Product')->load($prodId);

            $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

            foreach ($sourceItems as $sourceItemId => $sourceItem) {
                if ($sourceItem->getSourceCode() == 'SWHS') {
                    $swhsQty = $swhsQty * $sourceItem->getQuantity();
                } elseif ($sourceItem->getSourceCode() == 'MELB') {
                    $melbQty = $melbQty * $sourceItem->getQuantity();
                }
            }
        }
        
        $this->logger->info("isSwhs: " . $isSwhs); 
        $this->logger->info("isMelb: " . $isMelb); 
        $this->logger->info("swhsQty: " . $swhsQty); 
        $this->logger->info("melbQty: " . $melbQty); 
        
        if (($isSwhs == 1 && $carrierCode == 'nextdaydelivery' && $swhsQty <= 0) || 
                ($isMelb == 1 && $carrierCode == 'nextdaydelivery' && $melbQty <= 0)) {
            return false;
        }
        
        return $proceed($carrierCode, $request);
        
    }
   
}