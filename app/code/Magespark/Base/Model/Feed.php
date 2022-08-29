<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Model;

use MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection\ExistsFactory;
use MageSpark\Base\Model\Source\NotificationType;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\Notification\MessageInterface;
use MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection\Expired;
use MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection\ExpiredFactory;
use MageSpark\Base\Helper\Module;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\AdminNotification\Model\InboxFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Feed
 * @package MageSpark\Base\Model
 */
class Feed
{
    /**
     * Define constant variables
     */
    const HOUR_MIN_SEC_VALUE = 60 * 60 * 24;
    const REMOVE_EXPIRED_FREQUENCY = 60 * 60 * 6; //4 times per day
    const XML_LAST_UPDATE = 'magespark_base/system_value/last_update';
    const XML_FREQUENCY_PATH = 'magespark_base/notifications/frequency';
    const XML_FIRST_MODULE_RUN = 'magespark_base/system_value/first_module_run';
    const XML_LAST_REMOVMENT = 'magespark_base/system_value/remove_date';
    const URL_NEWS = 'magespark.com/feed-news-segments.xml';//do not use https:// or http

    /**
     * @var array
     */
    private $magesparkModules = [];

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var InboxFactory
     */
    private $inboxFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ExpiredFactory
     */
    private $expiredFactory;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ExistsFactory
     */
    private $inboxExistsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Url
     */
    private $parseurl;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * Feed constructor.
     *
     * @param ConfigInterface $config
     * @param ReinitableConfigInterface $reinitableConfig
     * @param WriterInterface $configWriter
     * @param CurlFactory $curlFactory
     * @param InboxFactory $inboxFactory
     * @param ProductMetadataInterface $productMetadata
     * @param ScopeConfigInterface $scopeConfig
     * @param ExpiredFactory $expiredFactory
     * @param ModuleListInterface $moduleList
     * @param ExistsFactory $inboxExistsFactory
     * @param StoreManagerInterface $storeManager
     * @param Url $parseurl
     * @param Module $moduleHelper
     */
    public function __construct(
        ConfigInterface $config,
        ReinitableConfigInterface $reinitableConfig,
        WriterInterface $configWriter,
        CurlFactory $curlFactory,
        InboxFactory $inboxFactory,
        ProductMetadataInterface $productMetadata,
        ScopeConfigInterface $scopeConfig,
        ExpiredFactory $expiredFactory,
        ModuleListInterface $moduleList,
        ExistsFactory $inboxExistsFactory,
        StoreManagerInterface $storeManager,
        Url $parseurl,
        Module $moduleHelper
    ) {
        $this->config = $config;
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->curlFactory = $curlFactory;
        $this->productMetadata = $productMetadata;
        $this->inboxFactory = $inboxFactory;
        $this->scopeConfig = $scopeConfig;
        $this->expiredFactory = $expiredFactory;
        $this->moduleList = $moduleList;
        $this->inboxExistsFactory = $inboxExistsFactory;
        $this->storeManager = $storeManager;
        $this->moduleHelper = $moduleHelper;
        $this->parseurl = $parseurl;
    }

    /**
     * Check the Frequency update
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    public function checkUpdate()
    {
        if ($this->getFrequency() + $this->getLastUpdate() > time()) {
            return $this;
        }

        $allowedNotifications = $this->getAllowedTypes();
        if (empty($allowedNotifications) || in_array(NotificationType::UNSUBSCRIBE_ALL, $allowedNotifications)) {
            return $this;
        }

        $feedData = null;
        $maxPriority = 0;

        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            $installDate = $this->getFirstModuleRun();
            foreach ($feedXml->channel->item as $item) {
                if (!array_intersect($this->convertToArray($item->type), $allowedNotifications)
                    || (int)$item->version == 1 // for magento One
                    || ((string)$item->edition && (string)$item->edition != $this->getCurrentEdition())
                ) {
                    continue;
                }

                $priority =(int)$item->priority ?: 1;
                if ($priority <= $maxPriority) {
                    continue; //add only one with the highest priority
                }

                if (!$this->validateByExtension((string)$item->extension)) {
                    continue;
                }

                if (!$this->validateByMageSparkCount($item->magespark_module_qty)) {
                    continue;
                }

                if (!$this->validateByNotInstalled((string)$item->magespark_module_not)) {
                    continue;
                }

                if (!$this->validateByExtension((string)$item->third_party_modules, true)) {
                    continue;
                }

                if (!$this->validateByDomainZone((string)$item->domain_zone)) {
                    continue;
                }

                if ($this->isItemExists($item)) {
                    continue;
                }

                $date = strtotime((string)$item->pubDate);
                $expired =(string)$item->expirationDate ? strtotime((string)$item->expirationDate) : null;
                if ($installDate <= $date
                    && (!$expired || $expired > gmdate('U'))
                ) {
                    $maxPriority = $priority;
                    $expired = $expired ? date('Y-m-d H:i:s', $expired) : null;

                    $feedData = [
                        'severity' => MessageInterface::SEVERITY_NOTICE,
                        'date_added' => date('Y-m-d H:i:s', $date),
                        'expiration_date' => $expired,
                        'title' => $this->convertString($item->title),
                        'description' => $this->convertString($item->description),
                        'url' => $this->convertString($item->link),
                        'is_magespark' => 1
                    ];
                }
            }

            if ($feedData) {
                /** @var \Magento\AdminNotification\Model\Inbox $inbox */
                $inbox = $this->inboxFactory->create();
                $inbox->parse([$feedData]);
            }
        }
        $this->setLastUpdate();

        return $this;
    }

    /**
     * Converting string to array
     *
     * @param $value
     * @return array
     */
    private function convertToArray($value)
    {
        return explode(',', (string)$value);
    }

    /**
     * Return existence of an item
     *
     * @param \SimpleXMLElement $item
     * @return bool
     */
    private function isItemExists(\SimpleXMLElement $item)
    {
        return $this->inboxExistsFactory->create()->execute($item);
    }

    /**
     * Getting current edition of module
     *
     * @return string
     */
    protected function getCurrentEdition()
    {
        return $this->productMetadata->getEdition() == 'Community' ? 'ce' : 'ee';
    }

    /**
     * Remove expired frequency
     *
     * @return $this
     */
    public function removeExpiredItems()
    {
        if ($this->getLastRemovement() + self::REMOVE_EXPIRED_FREQUENCY > time()) {
            return $this;
        }

        /** @var Expired $collection */
        $collection = $this->expiredFactory->create();
        foreach ($collection as $model) {
            $model->setIsRemove(1)->save();
        }

        $this->setLastRemovement();

        return $this;
    }

    /**
     * Get feed data with product metadata
     *
     * @return \SimpleXMLElement|false
     */
    public function getFeedData()
    {
        /** @var Curl $curlObject */
        $curlObject = $this->curlFactory->create();
        $curlObject->setConfig(
            [
                'timeout'   => 2,
                'useragent' => $this->productMetadata->getName()
                    . '/' . $this->productMetadata->getVersion()
                    . ' (' . $this->productMetadata->getEdition() . ')'
            ]
        );
        $curlObject->write(\Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $result = $curlObject->read();

        if ($result === false || $result === '') {
            return false;
        }

        $result = preg_split('/^\r?$/m', $result, 2);
        $result = trim($result[1]);

        $curlObject->close();

        try {
            $xml = new \SimpleXMLElement($result);
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Get allow notification with type
     *
     * @return array
     */
    private function getAllowedTypes()
    {
        $allowedNotifications = $this->getModuleConfig('notifications/type');
        $allowedNotifications = explode(',', $allowedNotifications);

        return $allowedNotifications;
    }

    /**
     * Convert the predefined characters to HTML entities
     *
     * @param \SimpleXMLElement $data
     * @return string
     */
    private function convertString(\SimpleXMLElement $data)
    {
        $data = htmlspecialchars((string)$data);
        return $data;
    }

    /**
     * Get Frequency with path and value
     *
     * @return int
     */
    private function getFrequency()
    {
        return $this->config->getValue(self::XML_FREQUENCY_PATH) * self::HOUR_MIN_SEC_VALUE;
    }

    /**
     * Get feed url with current schema
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getFeedUrl()
    {
        $scheme = $this->getCurrentScheme();
        $url = $scheme ?: 'http://';

        return $url . self::URL_NEWS;
    }

    /**
     * Get last update value
     *
     * @return int
     */
    private function getLastUpdate()
    {
        return $this->config->getValue(self::XML_LAST_UPDATE);
    }

    /**
     * Set the last update
     *
     * @return $this
     */
    private function setLastUpdate()
    {
        $this->configWriter->save(self::XML_LAST_UPDATE, time());
        $this->reinitableConfig->reinit();

        return $this;
    }

    /**
     * Get xml value of the module
     *
     * @return int|mixed
     */
    private function getFirstModuleRun()
    {
        $result = $this->config->getValue(self::XML_FIRST_MODULE_RUN);
        if (!$result) {
            $result = time();
            $this->configWriter->save(self::XML_FIRST_MODULE_RUN, $result);
            $this->reinitableConfig->reinit();
        }

        return $result;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    private function getModuleConfig($path, $storeId = null)
    {

//        return $this->getValue(
//            'magespark_base/' . $path,
//            ScopeInterface::SCOPE_STORE,
//            $storeId
//        );
    }

    /**
     * Get the value of last Removement
     *
     * @return int
     */
    private function getLastRemovement()
    {
        return $this->config->getValue(self::XML_LAST_REMOVMENT);
    }

    /**
     * Set the value of last Removement
     *
     * @return $this
     */
    private function setLastRemovement()
    {
        $this->configWriter->save(self::XML_LAST_REMOVMENT, time());
        $this->reinitableConfig->reinit();

        return $this;
    }

    /**
     * Get the install mage spark extention
     *
     * @return array|string[]
     */
    private function getInstalledMageSparkExtensions()
    {
        if (!$this->magesparkModules) {
            $modules = $this->moduleList->getNames();

            $dispatchResult = new \Magento\Framework\DataObject($modules);
            $modules = $dispatchResult->toArray();

            $modules = array_filter(
                $modules,
                function ($item) {
                    return strpos($item, 'MageSpark_') !== false;
                }
            );
            $this->magesparkModules = $modules;
        }

        return $this->magesparkModules;
    }

    /**
     * Get name of all extensions
     *
     * @return array|string[]
     */
    private function getAllExtensions()
    {
        $modules = $this->moduleList->getNames();

        $dispatchResult = new \Magento\Framework\DataObject($modules);
        $modules = $dispatchResult->toArray();

        return $modules;
    }

    /**
     * Return validate by extension by value
     *
     * @param string $extensions
     * @return bool
     */
    private function validateByExtension($extensions, $allModules = false)
    {
        if ($extensions) {
            $result = false;
            $extensions = $this->validateExtensionValue($extensions);

            if ($extensions) {
                $installedModules = $allModules ? $this->getAllExtensions() : $this->getInstalledMageSparkExtensions();
                $intersect = array_intersect($extensions, $installedModules);
                if ($intersect) {
                    $result = true;
                }
            }
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Return extension which not validated
     *
     * @param string $extensions
     * @return bool
     */
    private function validateByNotInstalled($extensions)
    {
        if ($extensions) {
            $result = false;
            $extensions = $this->validateExtensionValue($extensions);

            if ($extensions) {
                $installedModules = $this->getInstalledMageSparkExtensions();
                $diff = array_diff($extensions, $installedModules);
                if ($diff) {
                    $result = true;
                }
            }
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Validate by extention value
     *
     * @param string $extensions
     * @return array
     */
    private function validateExtensionValue($extensions)
    {
        $extensions = explode(',', $extensions);
        $extensions = array_filter($extensions, function ($item) {
            return strpos($item, '_1') === false;
        });

        $extensions = array_map(function ($item) {
            return str_replace('_2', '', $item);
        }, $extensions);

        return $extensions;
    }

    /**
     * Return installed magespark extension
     *
     * @param $counts
     * @return bool
     */
    private function validateByMageSparkCount($counts)
    {
        $result = true;

        $countString = (string)$counts;
        if ($countString) {
            $moreThan = null;
            $result = false;

            $position = strpos($countString, '>');
            if ($position !== false) {
                $moreThan = substr($countString, $position + 1);
                $moreThan = explode(',', $moreThan);
                $moreThan = array_shift($moreThan);
            }

            $counts = $this->convertToArray($counts);
            $magesparkModules = $this->getInstalledMageSparkExtensions();
            $dependModules = $this->getDependModules($magesparkModules);
            $magesparkModules = array_diff($magesparkModules, $dependModules);

            $magesparkCount = count($magesparkModules);

            if ($magesparkCount
                && (in_array($magesparkCount, $counts)
                    || ($moreThan && $magesparkCount >= $moreThan)
                )
            ) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Validating by domain zone
     *
     * @param $zones
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateByDomainZone($zones)
    {
        $result = true;
        if ($zones) {
            $zones = $this->convertToArray($zones);
            $currentZone = $this->getDomainZone();

            if (!in_array($currentZone, $zones)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Get all domain zone by its base url
     *
     * @return mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getDomainZone()
    {
        $domain = '';
        $url = $this->storeManager->getStore()->getBaseUrl();
        $components = $this->parseurl->__parseUrl($url);

        if (isset($components['host'])) {
            $host = explode('.', $components['host']);
            $domain = end($host);
        }

        return $domain;
    }

    /**
     * Get the current schema name
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentScheme()
    {
        $scheme = '';
        $url = $this->storeManager->getStore()->getBaseUrl();
        $components = $this->parseurl->__parseUrl($url);

        if (isset($components['scheme'])) {
            $scheme = $components['scheme'] . '://';
        }

        return $scheme;
    }

    /**
     * Get deended module name
     *
     * @param $magesparkModules
     * @return array
     */
    private function getDependModules($magesparkModules)
    {
        $depend = [];
        $result = [];
        $dataName = [];
        foreach ($magesparkModules as $module) {
            $data = $this->moduleHelper->getModuleInfo($module);
            if (isset($data['name'])) {
                $dataName[$data['name']] = $module;
            }

            if (isset($data['require']) and is_array($data['require'])) {
                foreach ($data['require'] as $requireItem => $version) {
                    if (strpos($requireItem, 'magespark') !== false) {
                        $depend[] = $requireItem;
                    }
                }
            }
        }

        $depend = array_unique($depend);
        foreach ($depend as $item) {
            if (isset($dataName[$item])) {
                $result[] = $dataName[$item];
            }
        }

        return $result;
    }
}
