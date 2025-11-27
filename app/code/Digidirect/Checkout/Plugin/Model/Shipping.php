<?php

namespace Digidirect\Checkout\Plugin\Model;

use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class Shipping {

    protected $logger;
    protected $rateCache = [];
    protected $productCache = [];

    public function __construct(
        GetSourceItemsBySku $getSourceItemsBySku,
        \Digidirect\SellerShipping\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
                                         $carrierCode,
                                         $request
    ) {
        $postCode = $request->getDestPostcode();
        $countryId = $request->getDestCountryId();
        $items = $request->getAllItems();

        // Create a cache key based on essential request parameters
        $itemsKey = [];
        foreach ($items as $item) {
            $itemsKey[] = $item->getProductId() . '_' . $item->getQty();
        }

        $cacheKey = md5(serialize([
            $carrierCode,
            $postCode,
            $countryId,
            $itemsKey,
            $this->helperData->hasMarketplacerSeller()
        ]));

        // Return cached result if available for this request
        if (isset($this->rateCache[$cacheKey])) {
            // Optional: Log cache hits (comment out after testing)
            // $this->logger->info("[CACHE HIT] Carrier: {$carrierCode}, PostCode: {$postCode}");
            return $this->rateCache[$cacheKey];
        }

        // Log only when actually processing (not using cache)
        $this->logger->info("[PROCESSING] Carrier: {$carrierCode}, PostCode: {$postCode}, Country: {$countryId}");

        // Proceed with original rate collection first
        $result = $proceed($carrierCode, $request);

        // Only do heavy filtering logic if we have rates to filter
        $originalRates = $subject->getResult()->getAllRates();

        if (empty($originalRates)) {
            $this->rateCache[$cacheKey] = $result;
            return $result;
        }

        $strathfieldPostCodes = [2128,2161,2112,2000,2216,2750,2036,2065,2229,2060,2155,2770,2154,2019,2100,2007,2015,2144,2136,2567,2138,2121,2087,2044,2142,2164,2069,2127,2166,2122,2178,2140,2132,2177,2170,2213,2114,2135,2193,2229,2153,2141,2067,2137,2138,2118,2194,2156,2143,2155,2767,2151,2747,2084,2747,2167,2099,2076,2080,2150,2171,2175,2120,2125,2199,2170,2145,2018,2142,2191,2160,2163,2117,2176,2170,2765,2176,2200,2165,2170,2145,2034,2116,2037,2168,2019,2085,2113,2196,2000,2761,2170,2205,2200,2162,2035,2567,2558,2033,2192,2134,2208,2217,2190,2170,2022,2230,2212,2204,2557,2763,2151,2142,2009,2759,2126,2077,2131,2205,2107,2560,2164,2560,2148,2171,2161,2210,2136,2066,2565,2560,2160,2195,2560,2046,2219,2166,2152,2228,2110,2218,2142,2223,2566,2760,2224,2088,2203,2749,2071,2170,2566,2101,2195,2198,2220,2567,2196,2100,2164,2197,2759,2207,2207,2221,2769,2141,2171,2770,2217,2148,2231,2760,2213,2171,2020,2036,2174,2768,2168,2765,2234,2171,2216,2165,2760,2127,2166,2147,2146,2564,2761,2209,2143,2565,2770,2205,2570,2565,2173,2179,2170,2168,2046,2557,2115,2207,2560,2161,2145,2229,2756,2168,2117,2143,2566,2145,2168,2099,2765,2206,2168,2179,2176,2171,2036,2211,2161,2166,2171,2017,2155,2165,2130,2010,2219,2218,2230,2041,2113,2210,2209,2230,2073,2750,2761,2767,2559,2556,2162,2147,2142,2560,2000,2066,2153,2206,2095,2024,2070,2768,2137,2176,2086,2560,2177,2072,2127,2234,2065,2049,2156,2046,2217,2193,2221,2138,2147,2172,2122,2068,2747,2030,2168,2050,2035,2030,2040,2079,2046,2555,2557,2066,2105,2567,2756,2016,2176,2081,2145,2176,2217,2074,2133,2565,2176,2026,2031,2145,2228,2216,2220,2026,2044,2032,2075,2765,2234,2148,2117,2745,2762,2034,2166,2211,2232,2038,2233,2137,2170,2111,2114,2145,2767,2155,2155,2074,2018,2560,2770,2190,2227,2120,2761,2037,2153,2214,2222,2112,2046,2047,2213,2064,2049,2750,2232,2097,2017,2092,2770,2150,2190,2061,2100,2074,2748,2745,2073,2089,2040,2100,2069,2031,2048,2042,2766,2761,2232,2158,2007,2065,2085,2030,2103,2021,2023,2567,2750,2137,2153,2119,2232,2052,2045,2765,2165,2234,2008,2000,2060,2093,2766,2046,2026,2010,2146,2212,2163,2140,2119,2747,2761,2099,2114,2065,2082,2223,2093,2068,2136,2760,2112,2036,2077,2121,2043,2028,2747];
        $melbPostCodes = [3053,3054,3008,3002,3031,3031,3000,3052,3207,3006,3141,3003,3206,3183,3184,3206,3185,3182,3183,3182,3067,3078,3121,3068,3066,3121,3078,3065,3068,3054,3121,3081,3088,3083,3084,3095,3088,3084,3081,3081,3079,3079,3093,3094,3084,3084,3087,3087,3085,3083,3070,3072,3073,3071,3049,3047,3061,3048,3064,3047,3043,3059,3047,3036,3048,3045,3064,3062,3043,3049,3428,3063,3063,3040,3042,3032,3034,3040,3041,3040,3033,3039,3042,3041,3041,3032,3056,3057,3055,3058,3058,3060,3046,3043,3046,3046,3044,3044,3089,3095,3095,3090,3091,3076,3075,3082,3074,3147,3103,3124,3126,3146,3122,3123,3101,3102,3127,3129,3127,3179,3105,3108,3109,3114,3106,3107,3113,3134,3134,3130,3130,3130,3128,3129,3128,3125,3151,3131,3132,3131,3133,3193,3193,3186,3187,3192,3185,3188,3190,3191,3174,3174,3171,3204,3165,3162,3145,3161,3162,3163,3185,3163,3204,3163,3204,3195,3195,3169,3169,3172,3196,3202,3194,3189,3194,3195,3195,3147,3148,3168,3150,3166,3166,3149,3170,3168,3166,3166,3167,3150,3143,3144,3144,3145,3181,3142,3181,3021,3020,3022,3012,3023,3049,3023,3037,3021,3038,3038,3036,3042,3021,3021,3020,3020,3037,3038,3018,3028,3025,3028,3015,3015,3028,3018,3015,3016,3016,3019,3011,3012,3012,3032,3011,3012,3012,3013,3023,3023,3023,3023,3029,3029,3026,3030,3006,3103,3111,3027,3205,3188,3020,3104,3041,3088,3085,3058,3051,3026,3148,3170,3032];

        $is3whs = in_array($postCode, $strathfieldPostCodes) ? 1 : 0;
        $isMelb = in_array($postCode, $melbPostCodes) ? 1 : 0;

        // Initialize method codes to remove
        $methodCodeToRemove = ['AP_intlshipping', 'AP_intlshippingnz'];

        if ($countryId == 'AU') {
            // Only load products and check inventory for AU when we need to filter nextdayship
            $needsInventoryCheck = false;

            // Quick check if we need to do inventory checking
            foreach ($originalRates as $rate) {
                if ($rate->getMethod() == 'nextdayship') {
                    $needsInventoryCheck = true;
                    break;
                }
            }

            if ($needsInventoryCheck) {
                $this->logger->info("[INVENTORY CHECK] Loading products for nextdayship validation");

                $s3whsQty = 1;
                $melbQty = 1;
                $hasBulkyItem = false;

                foreach ($items as $item) {
                    $prodId = $item->getProductId();

                    // Use product cache to avoid reloading same products
                    if (!isset($this->productCache[$prodId])) {
                        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $this->productCache[$prodId] = $_objectManager->get('\Magento\Catalog\Model\Product')->load($prodId);
                    }

                    $product = $this->productCache[$prodId];
                    $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

                    if ($product->getData('bulky_item') && !$hasBulkyItem) {
                        $hasBulkyItem = true;
                    }

                    foreach ($sourceItems as $sourceItemId => $sourceItem) {
                        $getQty = $sourceItem->getQuantity();
                        if ($getQty < 0) {
                            $getQty = 0;
                        }
                        if ($sourceItem->getSourceCode() == '3WHS') {
                            $s3whsQty = $s3whsQty * $getQty;
                        } elseif ($sourceItem->getSourceCode() == 'MELB') {
                            $melbQty = $melbQty * $getQty;
                        }
                    }
                }

                // Determine if nextdayship should be removed
                $shouldRemoveNextDay = false;

                $shouldRemoveNextDay = false;

                if ($is3whs == 1 && $s3whsQty <= 0) {
                    // Strathfield postcode but no 3WHS stock
                    $shouldRemoveNextDay = true;
                } elseif ($isMelb == 1 && $melbQty <= 0) {
                    // Melbourne postcode but no Melbourne stock
                    $shouldRemoveNextDay = true;
                } elseif ($is3whs == 0 && $isMelb == 0) {
                    // Neither Strathfield nor Melbourne postcode
                    $shouldRemoveNextDay = true;
                }

                if ($this->helperData->hasMarketplacerSeller()) {
                    $shouldRemoveNextDay = true;
                }

                if ($shouldRemoveNextDay && !in_array('express_nextdayship', $methodCodeToRemove)) {
                    $methodCodeToRemove[] = 'express_nextdayship';
                }
            }

            // Filter rates for AU
            $this->filterShippingRates($subject, $originalRates, $methodCodeToRemove, false);

        } else {
            // For non-AU countries, remove domestic shipping methods
            $methodCodeToRemove = array_merge($methodCodeToRemove, [
                'standard_standard',
                'express_express',
                'express_nextdayship'
            ]);

            // Remove appropriate international method based on country
            if ($countryId == 'NZ') {
                if (!in_array('AP_intlshipping', $methodCodeToRemove)) {
                    $methodCodeToRemove[] = 'AP_intlshipping';
                }
            } else {
                if (!in_array('AP_intlshippingnz', $methodCodeToRemove)) {
                    $methodCodeToRemove[] = 'AP_intlshippingnz';
                }
            }

            // Filter rates for non-AU
            $this->filterShippingRates($subject, $originalRates, $methodCodeToRemove, false);
        }

        // Cache the result
        $this->rateCache[$cacheKey] = $result;

        return $result;
    }

    /**
     * Helper method to filter shipping rates
     *
     * @param bool $logRates Whether to log individual rates (set to false to reduce logs)
     */
    protected function filterShippingRates($subject, $originalRates, $methodCodeToRemove, $logRates = true)
    {
        $filteredResult = clone $subject->getResult();
        $reflection = new \ReflectionClass($filteredResult);
        $ratesProperty = $reflection->getProperty('_rates');
        $ratesProperty->setAccessible(true);
        $ratesProperty->setValue($filteredResult, []);

        foreach ($originalRates as $rate) {
            $fullMethodCode = $rate->getCarrier() . '_' . $rate->getMethod();

            if (!in_array($fullMethodCode, $methodCodeToRemove)) {
                // Only log if explicitly requested (reduces log noise)
                if ($logRates) {
                    $this->logger->info("fullMethodCode, " . $fullMethodCode);
                }
                $filteredResult->append($rate);
            }
        }

        // Replace original result with filtered one
        $reflection = new \ReflectionClass($subject);
        $resultProperty = $reflection->getProperty('_result');
        $resultProperty->setAccessible(true);
        $resultProperty->setValue($subject, $filteredResult);
    }
}
