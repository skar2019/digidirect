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
    * Always parse in store's configured timezone
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

       // Get store timezone
       $storeTimezone = new \DateTimeZone($this->timezone->getConfigTimezone());

       // List of possible datetime formats - Magento format FIRST
       $formats = [
           'd/m/Y H:i:s',      // 09/02/2026 17:47:00 (Magento default)
           'd/m/Y H:i',        // 09/02/2026 17:47
           'd/m/Y',            // 09/02/2026
           'd/m/y H:i:s',      // 09/02/26 17:47:00
           'm/d/Y H:i:s',      // 02/09/2026 17:47:00
           'm/d/y H:i:s',      // 02/09/26 17:47:00
           'Y-m-d H:i:s',      // 2026-02-09 17:47:00
           'm-d-Y H:i:s',      // 02-09-2026 17:47:00
           'Y/m/d H:i:s',      // 2026/02/09 17:47:00
           'm/d/Y H:i',        // 02/09/2026 17:47
           'Y-m-d H:i',        // 2026-02-09 17:47
           'm/d/Y',            // 02/09/2026
           'Y-m-d',            // 2026-02-09
       ];

       foreach ($formats as $format) {
           $dateTime = \DateTime::createFromFormat($format, $dateTimeString, $storeTimezone);
           if ($dateTime !== false) {
               // Validate that the parsed date makes sense
               $errors = \DateTime::getLastErrors();
               if ($errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                   $this->logger->info("Successfully parsed datetime '{$dateTimeString}' using format '{$format}' in timezone '{$storeTimezone->getName()}' => " . $dateTime->format('Y-m-d H:i:s T'));
                   return $dateTime;
               }
           }
       }

       // Fallback: try strtotime with timezone
       try {
           $timestamp = strtotime($dateTimeString);
           if ($timestamp !== false) {
               $dateTime = new \DateTime('@' . $timestamp);
               $dateTime->setTimezone($storeTimezone);
               $this->logger->info("Successfully parsed datetime '{$dateTimeString}' using strtotime in timezone '{$storeTimezone->getName()}' => " . $dateTime->format('Y-m-d H:i:s T'));
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

       // Get current datetime in store timezone
       $storeTimezone = $this->timezone->getConfigTimezone();
       $currentDateTime = new \DateTime('now', new \DateTimeZone($storeTimezone));

       $this->logger->info('========================================');
       $this->logger->info('Digidirect_CombinationPricing: Checking Active Combinations');
       $this->logger->info('Current datetime: ' . $currentDateTime->format('Y-m-d H:i:s T (e)'));
       $this->logger->info('Timezone: ' . $storeTimezone);
       $this->logger->info('Total combinations to check: ' . count($combinations));
       $this->logger->info('========================================');

       foreach ($combinations as $key => $combination) {
           $this->logger->info('--- Combination ' . $key . ' ---');
           $this->logger->info('Raw data: ' . json_encode($combination));

           // Validate required fields
           if (empty($combination['first_sku'])) {
               $this->logger->warning('✗ Skipped: first_sku is empty');
               continue;
           }

           if (empty($combination['second_sku'])) {
               $this->logger->warning('✗ Skipped: second_sku is empty');
               continue;
           }

           if (!isset($combination['fixed_price']) || $combination['fixed_price'] <= 0) {
               $this->logger->warning('✗ Skipped: fixed_price is invalid (' . ($combination['fixed_price'] ?? 'null') . ')');
               continue;
           }

           // Check start datetime
           if (!empty($combination['start_datetime'])) {
               $startDateTime = $this->parseDateTime($combination['start_datetime']);

               if ($startDateTime === null) {
                   $this->logger->warning('✗ Skipped: invalid start_datetime format: ' . $combination['start_datetime']);
                   continue;
               }

               $this->logger->info('Start check:');
               $this->logger->info('  Current: ' . $currentDateTime->format('Y-m-d H:i:s T'));
               $this->logger->info('  Start:   ' . $startDateTime->format('Y-m-d H:i:s T'));
               $this->logger->info('  Current >= Start? ' . ($currentDateTime >= $startDateTime ? 'YES ✓' : 'NO ✗'));

               if ($currentDateTime < $startDateTime) {
                   $this->logger->info('✗ Skipped: Not started yet');
                   continue;
               }
           } else {
               $this->logger->info('No start datetime - active from beginning');
           }

           // Check end datetime
           if (!empty($combination['end_datetime'])) {
               $endDateTime = $this->parseDateTime($combination['end_datetime']);

               if ($endDateTime === null) {
                   $this->logger->warning('✗ Skipped: invalid end_datetime format: ' . $combination['end_datetime']);
                   continue;
               }

               $this->logger->info('End check:');
               $this->logger->info('  Current: ' . $currentDateTime->format('Y-m-d H:i:s T'));
               $this->logger->info('  End:     ' . $endDateTime->format('Y-m-d H:i:s T'));
               $this->logger->info('  Current <= End? ' . ($currentDateTime <= $endDateTime ? 'YES ✓' : 'NO ✗'));

               if ($currentDateTime > $endDateTime) {
                   $this->logger->info('✗ Skipped: Expired');
                   continue;
               }
           } else {
               $this->logger->info('No end datetime - active forever');
           }

           $activeCombinations[] = [
               'first_sku' => trim($combination['first_sku']),
               'second_sku' => trim($combination['second_sku']),
               'fixed_price' => (float) $combination['fixed_price']
           ];

           $this->logger->info('✓✓✓ ACTIVE: ' . $combination['first_sku'] . ' + ' . $combination['second_sku'] . ' = $' . $combination['fixed_price']);
       }

       $this->logger->info('========================================');
       $this->logger->info('Total ACTIVE combinations: ' . count($activeCombinations));
       $this->logger->info('========================================');

       return $activeCombinations;
   }
}