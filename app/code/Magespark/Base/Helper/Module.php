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

namespace MageSpark\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use SimpleXMLElement;
use Zend\Http\Client\Adapter\Curl as CurlClient;
use Zend\Http\Response as HttpResponse;
use Zend\Uri\Http as HttpUri;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\App\Helper\Context;
use MageSpark\Base\Model\Serializer;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class Module
 *
 * @package MageSpark\Base\Helper
 */
class Module extends AbstractHelper
{
    /**
     * @const EXTENSIONS_PATH
     * @const URL_EXTENSIONS
     */
    const EXTENSIONS_PATH = 'msbase_extensions';
    const URL_EXTENSIONS = 'http://www.magespark.com/feed-extensions-m2.xml';

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var CurlClient
     */
    protected $curlClient;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var array|null
     */
    private $modulesData = null;

    /**
     * @var array
     */
    protected $restrictedModules = [
        'MageSpark_CommonRules',
        'MageSpark_Router'
    ];

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $fileSystem;

    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    /**
     * Module constructor.
     *
     * @param Context $context
     * @param Serializer $serializer
     * @param CacheInterface $cache
     * @param Reader $moduleReader
     * @param File $fileSystem
     * @param DecoderInterface $jsonDecoder
     * @param CurlClient $curl
     */
    public function __construct(
        Context $context,
        Serializer $serializer,
        CacheInterface $cache,
        Reader $moduleReader,
        File $fileSystem,
        DecoderInterface $jsonDecoder,
        CurlClient $curl
    ) {
        parent::__construct($context);

        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->curlClient = $curl;
        $this->moduleReader = $moduleReader;
        $this->fileSystem = $fileSystem;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Get array with info about all MageSpark Magento2 Extensions
     *
     * @return bool|mixed
     */
    public function getAllExtensions()
    {
        $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        if ($serialized === false) {
            $this->reload();
            $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        }
        $result = $this->serializer->unserialize($serialized);

        return $result;
    }

    /**
     * Save extensions data to magento cache
     *
     */
    public function reload()
    {
        $feedData = [];
        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $code = (string)$item->code;

                if (!isset($feedData[$code])) {
                    $feedData[$code] = [];
                }

                $feedData[$code][(string)$item->title] = [
                    'name'               => (string)$item->title,
                    'url'                => (string)$item->link,
                    'version'            => (string)$item->version,
                    'conflictExtensions' => (string)$item->conflictExtensions,
                    'guide'              => (string)$item->guide,
                ];
            }

            if ($feedData) {
                $this->cache->save($this->serialize($feedData), self::EXTENSIONS_PATH);
            }
        }
    }

    /**
     * Read data from xml file with curl
     *
     * @return bool|SimpleXMLElement
     */
    protected function getFeedData()
    {
        try {
            $curlClient = $this->getCurlClient();

            $location = self::URL_EXTENSIONS;
            $uri = new HttpUri($location);

            $curlClient->setOptions(
                [
                    'timeout' => 8
                ]
            );

            $curlClient->connect($uri->getHost(), $uri->getPort());
            $curlClient->write('GET', $uri, 1.0);
            $data = HttpResponse::fromString($curlClient->read());

            $curlClient->close();

            $xml = new SimpleXMLElement($data->getContent());
        } catch (\Exception $e) {
            return false;
        }
        return $xml;
    }

    /**
     * Returns the cURL client that is being used.
     *
     * @return CurlClient
     */
    public function getCurlClient()
    {
        if ($this->curlClient === null) {
            $this->curlClient = new CurlClient();
        }
        return $this->curlClient;
    }

    /**
     * Serialize the data
     *
     * @param $data
     * @return bool|string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * Return the RestrictedModules
     *
     * @return array
     */
    public function getRestrictedModules()
    {
        return $this->restrictedModules;
    }

    /**
     * Read info about extension from composer json file
     *
     * @param $moduleCode
     * @return array|mixed
     */
    public function getModuleInfo($moduleCode)
    {
        try {
            $dir = $this->moduleReader->getModuleDir('', $moduleCode);
            $file = $dir . '/composer.json';

            $string = $this->fileSystem->fileGetContents($file);
            $json = $this->jsonDecoder->decode($string);
        } catch (FileSystemException $e) {
            $json = [];
        }
        return $json;
    }

    /**
     * Set all extentions into module data
     *
     * @param $moduleCode
     * @return array
     */
    public function getFeedModuleData($moduleCode)
    {
        $moduleData = [];
        if ($this->modulesData === null || $this->modulesData === false) {
            $this->modulesData = $this->getAllExtensions();
        }

        if ($this->modulesData && isset($this->modulesData[$moduleCode])) {
            $module = $this->modulesData[$moduleCode];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }
            $moduleData = $module;
        }
        return $moduleData;
    }
}
