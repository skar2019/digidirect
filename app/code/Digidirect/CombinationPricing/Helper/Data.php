<?php
namespace Digidirect\CombinationPricing\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->timezone = $timezone;
        $this->serializer = $serializer;
        $this->logger = $logger;
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
            $this->logger->error('Error unserializing combinations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse datetime string with multiple format support
     * Prioritizes d/m/Y format (Magento default)
     *
     * @param string $dateTimeString
     * @return \DateTime|null
     */
    protected function parseDateTime($dateTimeString)
    {
        if (empty($dateTimeString)) {
            return null;
        }

        $dateTimeString = trim($dateTimeString);

        // List of possible datetime formats - Magento format FIRST
        $formats = [
            'd/m/Y H:i:s',      // 15/01/2025 14:30:00 (Magento default)
            'd/m/Y H:i',        // 15/01/2025 14:30
            'd/m/Y',            // 15/01/2025
            'd/m/y H:i:s',      // 15/01/25 14:30:00
            'm/d/Y H:i:s',      // 01/15/2025 14:30:00
            'm/d/y H:i:s',      // 01/15/25 14:30:00
            'Y-m-d H:i:s',      // 2025-01-15 14:30:00
            'm-d-Y H:i:s',      // 01-15-2025 14:30:00
            'Y/m/d H:i:s',      // 2025/01/15 14:30:00
            'm/d/Y H:i',        // 01/15/2025 14:30
            'Y-m-d H:i',        // 2025-01-15 14:30
            'm/d/Y',            // 01/15/2025
            'Y-m-d',            // 2025-01-15
        ];

        foreach ($formats as $format) {
            $dateTime = \DateTime::createFromFormat($format, $dateTimeString);
            if ($dateTime !== false) {
                // Validate that the parsed date makes sense
                $errors = \DateTime::getLastErrors();
                if ($errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                    $this->logger->info("Successfully parsed datetime '{$dateTimeString}' using format '{$format}' => " . $dateTime->format('Y-m-d H:i:s'));
                    return $dateTime;
                }
            }
        }

        // Fallback: try strtotime
        try {
            $timestamp = strtotime($dateTimeString);
            if ($timestamp !== false) {
                $dateTime = new \DateTime();
                $dateTime->setTimestamp($timestamp);
                $this->logger->info("Successfully parsed datetime '{$dateTimeString}' using strtotime => " . $dateTime->format('Y-m-d H:i:s'));
                return $dateTime;
            }
        } catch (\Exception $e) {
            $this->logger->warning("Failed to parse datetime using strtotime: {$dateTimeString}");
        }

        $this->logger->error("Failed to parse datetime: {$dateTimeString}");
        return null;
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

        $this->logger->info('Digidirect_CombinationPricing: Current datetime: ' . $currentDateTime->format('Y-m-d H:i:s'));
        $this->logger->info('Digidirect_CombinationPricing: Checking ' . count($combinations) . ' combinations');

        foreach ($combinations as $key => $combination) {
            $this->logger->info('=== Combination ' . $key . ' ===');
            $this->logger->info('Raw data: ' . json_encode($combination));

            // Validate required fields
            if (empty($combination['first_sku'])) {
                $this->logger->warning('Combination ' . $key . ' skipped: first_sku is empty');
                continue;
            }
            
            if (empty($combination['second_sku'])) {
                $this->logger->warning('Combination ' . $key . ' skipped: second_sku is empty');
                continue;
            }
            
            if (!isset($combination['fixed_price']) || $combination['fixed_price'] <= 0) {
                $this->logger->warning('Combination ' . $key . ' skipped: fixed_price is invalid (' . ($combination['fixed_price'] ?? 'null') . ')');
                continue;
            }

            // Check start datetime
            $startDateTime = null;
            if (!empty($combination['start_datetime'])) {
                $startDateTime = $this->parseDateTime($combination['start_datetime']);
                
                if ($startDateTime === null) {
                    $this->logger->warning('Combination ' . $key . ' skipped: invalid start_datetime format: ' . $combination['start_datetime']);
                    continue;
                }
                
                if ($currentDateTime < $startDateTime) {
                    $this->logger->info('Combination ' . $key . ' skipped: Not started yet (current: ' . $currentDateTime->format('Y-m-d H:i:s') . ', start: ' . $startDateTime->format('Y-m-d H:i:s') . ')');
                    continue;
                }
            }

            // Check end datetime
            $endDateTime = null;
            if (!empty($combination['end_datetime'])) {
                $endDateTime = $this->parseDateTime($combination['end_datetime']);
                
                if ($endDateTime === null) {
                    $this->logger->warning('Combination ' . $key . ' skipped: invalid end_datetime format: ' . $combination['end_datetime']);
                    continue;
                }
                
                if ($currentDateTime > $endDateTime) {
                    $this->logger->info('Combination ' . $key . ' skipped: Expired (current: ' . $currentDateTime->format('Y-m-d H:i:s') . ', end: ' . $endDateTime->format('Y-m-d H:i:s') . ')');
                    continue;
                }
            }

            $activeCombinations[] = [
                'first_sku' => trim($combination['first_sku']),
                'second_sku' => trim($combination['second_sku']),
                'fixed_price' => (float) $combination['fixed_price']
            ];

            $this->logger->info('✓ Combination ' . $key . ' is ACTIVE: ' . $combination['first_sku'] . ' + ' . $combination['second_sku'] . ' = $' . $combination['fixed_price']);
        }

        $this->logger->info('Digidirect_CombinationPricing: Total active combinations found: ' . count($activeCombinations));

        return $activeCombinations;
    }
}