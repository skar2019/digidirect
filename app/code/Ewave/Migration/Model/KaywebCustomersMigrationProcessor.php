<?php

declare(strict_types=1);

namespace Ewave\Migration\Model;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Ewave\Migration\Helper\Profiler;
use Ewave\Migration\Helper\Data;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class KaywebCustomersMigrationProcessor
 *
 * @author Michael Marchanka <michail.marchenko@ewave.com>
 */
class KaywebCustomersMigrationProcessor
{
    /**
     * Kayweb attribute => Magento attribute
     */
    private const MIGRATION_MAP = [
        'CUSTOMER' => [
            'email' => 'email',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'contact_number' => 'contact_number',
            'pronto_code' => 'pronto_account_id',
            'aipp_number' => 'aipp_number',
            'aipp_verified' => 'is_aipp_verified',
        ],
        'OTHER' => [
            'newsletter_subscriber' => 'subscribed',
        ],
    ];

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var Register
     */
    private $registerBlock;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param CustomerFactory $customerFactory
     * @param SubscriberFactory $subscriberFactory
     * @param Register $registerBlock
     * @param StoreManagerInterface $storeManager
     * @param Profiler $profiler
     * @param Data $helper
     */
    public function __construct(
        CustomerFactory $customerFactory,
        SubscriberFactory $subscriberFactory,
        Register $registerBlock,
        StoreManagerInterface $storeManager,
        Profiler $profiler,
        Data $helper
    ) {
        $this->customerFactory = $customerFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->registerBlock = $registerBlock;
        $this->storeManager = $storeManager;
        $this->profiler = $profiler;
        $this->helper = $helper;
    }

    /**
     * @param array $data
     * @param OutputInterface $output
     * @param int $offset
     * @param int $limit
     * @param bool $debug
     * @param bool $clear
     */
    public function process(
        OutputInterface $output,
        array $data,
        int $offset = 0,
        int $limit = 0,
        bool $debug = false,
        bool $clear = false
    ): void
    {
        $data = array_slice($data, $offset, $limit ? $limit : null);

        $progressBar = new ProgressBar($output, count($data));
        $progressBar->setFormat($debug ? 'debug' : 'verbose');
        $progressBar->start();

        foreach ($data as $i => $customerData) {
            try {
                $this->createCustomer($customerData, $clear);
                $progressBar->advance();
                if ($debug && $i % 500 == 0) {
                    $output->writeln('');
                    $output->writeln($this->profiler->getProcessMemoryUsage());
                }
            } catch (\Exception $e) {
                $output->writeln('');
                $output->writeln(sprintf('Error: %s', $e->getMessage()));
                $output->writeln(sprintf('Customer Data: %s', print_r($customerData, true)));
            }
        }

        if ($debug) {
            $output->writeln('');
            $output->writeln($this->profiler->getProcessMemoryUsage());
        }

        $progressBar->finish();
    }

    /**
     * @param array $customerData
     * @param bool $clear
     *
     * @throws \Exception
     */
    private function createCustomer(array $customerData, bool $clear): void
    {
        $mapKeys = array_keys(self::MIGRATION_MAP['CUSTOMER']);
        $customerRequestParams = [];

        foreach ($customerData as $attrKey => $attrValue) {
            if (!in_array($attrKey, $mapKeys) || null === $attrValue) {
                continue;
            }

            $methodName = 'extract' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($attrKey);
            if (!method_exists($this, $methodName)) {
                $methodName = 'extractDefault';
            }

            $this->{$methodName}($attrKey, $attrValue, $customerRequestParams);
        }

        if (!$customerRequestParams
            || empty($customerRequestParams['email'])
            || empty($customerRequestParams['firstname'])
            || empty($customerRequestParams['lastname'])
        ) {
            return;
        }

        $store = $this->storeManager->getStore();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($store->getWebsiteId());
        $customer->loadByEmail($customerRequestParams['email']);

        if ($customer->getId() && $clear) {
            $customer->delete();
            $customer = $this->customerFactory->create();
        }

        $customerDataModel = $customer->getDataModel();
        $customerDataModel->setWebsiteId($store->getWebsiteId())
            ->setStoreId($store->getId())
            ->setEmail($customerRequestParams['email'])
            ->setFirstname($customerRequestParams['firstname'])
            ->setLastname($customerRequestParams['lastname']);

        foreach ($customerRequestParams as $customerRequestParamKey => $customerRequestParamValue) {
            $customerDataModel->setCustomAttribute($customerRequestParamKey, $customerRequestParamValue);
        }

        // We do not use customer repository in order to avoid memory leak
        $customer->updateData($customerDataModel);
        $customer->save();

        $customerDataModel = $customer->getDataModel();

        $toSubscribe = (bool) $customerData['newsletter_subscriber'] ?? false;
        if ($this->registerBlock->isNewsletterEnabled()) {
            $subscription = $this->subscriberFactory->create();
            $subscription->loadByEmail($customerDataModel->getEmail());
            $subscriptionStatus = $toSubscribe ? $subscription::STATUS_SUBSCRIBED : $subscription::STATUS_UNSUBSCRIBED;
            $subscription->setStatus($subscriptionStatus)
                ->setCustomerId($customerDataModel->getId())
                ->setStoreId($customerDataModel->getStoreId())
                ->setEmail($customerDataModel->getEmail())
                ->setStatusChanged(true)
                ->save();
        }
    }

    /**
     * @param string $attrKey
     * @param mixed $attrValue
     * @param array $customerRequestParams
     */
    private function extractDefault(string $attrKey, $attrValue, array &$customerRequestParams): void
    {
        $attrKey = self::MIGRATION_MAP['CUSTOMER'][$attrKey];
        $customerRequestParams[$attrKey] = $attrValue;
    }

    /**
     * @param string $attrKey
     * @param mixed $attrValue
     * @param array $customerRequestParams
     */
    private function extractFirstname(string $attrKey, $attrValue, array &$customerRequestParams): void
    {
        $attrValue = $this->helper->htmlEntityDecode($attrValue, ENT_QUOTES | ENT_HTML5);
        $this->extractDefault($attrKey, $attrValue, $customerRequestParams);
    }

    /**
     * @param string $attrKey
     * @param mixed $attrValue
     * @param array $customerRequestParams
     */
    private function extractLastname(string $attrKey, $attrValue, array &$customerRequestParams): void
    {
        $attrValue = $this->helper->htmlEntityDecode($attrValue, ENT_QUOTES | ENT_HTML5);
        $this->extractDefault($attrKey, $attrValue, $customerRequestParams);
    }
}
