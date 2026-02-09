<?php
namespace Digidirect\CombinationPricing\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'digidirect_combination_pricing/general/enabled';
    const XML_PATH_COMBINATIONS = 'digidirect_combination_pricing/general/combinations';

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->timezone = $timezone;
        $this->serializer = $serializer;
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get all combinations
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCombinations($storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_COMBINATIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($value)) {
            return [];
        }

        try {
            $combinations = $this->serializer->unserialize($value);
            return is_array($combinations) ? $combinations : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get active combinations within datetime range
     *
     * @param int|null $storeId
     * @return array
     */
    public function getActiveCombinations($storeId = null)
    {
        $combinations = $this->getCombinations($storeId);
        $activeCombinations = [];
        $currentDateTime = $this->timezone->date();

        foreach ($combinations as $combination) {
            // Validate required fields
            if (empty($combination['first_sku']) || 
                empty($combination['second_sku']) || 
                !isset($combination['fixed_price']) || 
                $combination['fixed_price'] <= 0) {
                continue;
            }

            // Check start datetime
            if (!empty($combination['start_datetime'])) {
                try {
                    $startDateTime = new \DateTime($combination['start_datetime'], $this->timezone->getConfigTimezone());
                    if ($currentDateTime < $startDateTime) {
                        continue;
                    }
                } catch (\Exception $e) {
                    $this->_logger->warning('Invalid start_datetime format: ' . $combination['start_datetime']);
                    continue;
                }
            }

            // Check end datetime
            if (!empty($combination['end_datetime'])) {
                try {
                    $endDateTime = new \DateTime($combination['end_datetime'], $this->timezone->getConfigTimezone());
                    if ($currentDateTime > $endDateTime) {
                        continue;
                    }
                } catch (\Exception $e) {
                    $this->_logger->warning('Invalid end_datetime format: ' . $combination['end_datetime']);
                    continue;
                }
            }

            $activeCombinations[] = [
                'first_sku' => trim($combination['first_sku']),
                'second_sku' => trim($combination['second_sku']),
                'fixed_price' => (float) $combination['fixed_price']
            ];
        }

        return $activeCombinations;
    }
}