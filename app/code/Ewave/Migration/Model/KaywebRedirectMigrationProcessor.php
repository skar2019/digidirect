<?php

declare(strict_types=1);

namespace Ewave\Migration\Model;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Ewave\Migration\Helper\Profiler;
use Ewave\Migration\Helper\Data;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class KaywebOrderMigrationProcessor
 * @package Ewave\Migration\Model
 */
class KaywebRedirectMigrationProcessor
{
    const DEFAULT_ATTRIBUTE = 'sku';
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
     * @var File
     */
    private $file;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * KaywebRedirectMigrationProcessor constructor.
     * @param Register $registerBlock
     * @param StoreManagerInterface $storeManager
     * @param Profiler $profiler
     * @param Data $helper
     * @param File $file
     * @param JsonSerializer $jsonSerializer
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        Register $registerBlock,
        StoreManagerInterface $storeManager,
        Profiler $profiler,
        Data $helper,
        File $file,
        JsonSerializer $jsonSerializer,
        CollectionFactory $productCollectionFactory
    ) {
        $this->registerBlock = $registerBlock;
        $this->storeManager = $storeManager;
        $this->profiler = $profiler;
        $this->helper = $helper;
        $this->file = $file;
        $this->jsonSerializer = $jsonSerializer;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param OutputInterface $output
     * @param \ArrayIterator $productIterator
     * @param bool $debug
     * @return array
     */
    public function process(
        OutputInterface $output,
        \ArrayIterator $productIterator,
        bool $debug = false
    ) {

        $q = $productIterator->count();
        $progressBar = new ProgressBar($output, $q);
        $progressBar->setFormat($debug ? 'debug' : 'verbose');
        $progressBar->start();

        /**
         * @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection
         */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(self::DEFAULT_ATTRIBUTE);
        $collection->addAttributeToFilter(self::DEFAULT_ATTRIBUTE, ['in' => $productIterator->getArrayCopy()]);
        $urlRewrite = [];
        foreach ($collection as $key => $product) {
            try {
                $progressBar->advance();
                $urlParsed = explode('/', $product->getUrlModel()->getUrl($product));
                $url = array_pop($urlParsed);

                $urlRewrite[(string)$product->getSku()] = $url;
                if ($debug && $key % 500 == 0) {
                    $output->writeln('');
                    $output->writeln($this->profiler->getProcessMemoryUsage());
                }
            } catch (\Exception $e) {
                $output->writeln('');
                $output->writeln(sprintf('Error: %s', $e->getMessage()));
            }
        }

        $allData = array_flip($productIterator->getArrayCopy());
        $notExecuted = array_diff_key($allData, $urlRewrite);
        $progressBar->finish();

        return ['all' => $allData, 'exist' => $urlRewrite, 'notExecuted' => $notExecuted];
    }
}
