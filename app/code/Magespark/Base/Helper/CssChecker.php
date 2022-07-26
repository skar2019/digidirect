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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Area;

/**
 * Class CssChecker
 *
 * @package MageSpark\Base\Helper
 */
class CssChecker extends AbstractHelper
{
    const CSS_EXIST_PATH = 'css/styles-m.css';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Repository
     */
    private $asset;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var File
     */
    private $file;

    /**
     * CssChecker constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param Repository $asset
     * @param Emulation $appEmulation
     * @param File $file
     * @param Filesystem $filesystem
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        Repository $asset,
        Emulation $appEmulation,
        File $file,
        Filesystem $filesystem
    ) {
        parent::__construct($context);

        $this->filesystem = $filesystem;
        $this->asset = $asset;
        $this->appEmulation = $appEmulation;
        $this->storeManager = $storeManager;
        $this->file = $file;
    }

    /**
     * Get details of currupted websites
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCorruptedWebsites()
    {
        $pubStaticPath = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)->getAbsolutePath();
        $failWebsites = [];
        $websites = [];

        foreach ($this->storeManager->getStores() as $store) {
            $websiteId = $store->getWebsiteId();
            $websiteName = $this->storeManager->getWebsite()->getName();

            if (in_array($websiteId, $websites)) {
                continue;
            } else {
                $websites[] = $websiteId;
            }

            $storeId = $store->getStoreId();

            $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
            $urlPath = $this->asset->getUrlWithParams(self::CSS_EXIST_PATH, []);
            $this->appEmulation->stopEnvironmentEmulation();

            $cssPath = $pubStaticPath . strstr($urlPath, 'frontend/');

            if (!$this->file->fileExists($cssPath)) {
                $failWebsites[$websiteId] = $websiteName;
            }
        }

        return $failWebsites;
    }
}
