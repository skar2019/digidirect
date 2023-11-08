<?php
 
namespace Digidirect\ProductPosition\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
 
class ReadCsv extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var Csv
     */
    protected $csv;
    /**
     * @var File
     */
    protected $file;
    
    
    protected $_logger;
    
 
    public function __construct(
        DirectoryList $directoryList,
        Csv $csv,
        File $file,
        \Magento\Framework\App\Action\Context $context,
        ProductCollectionFactory $productCollectionFactory,  
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        CategoryResource $categoryResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory,
    )
    {
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->file = $file;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->_logger = $logger;
        $this->_productFactory = $productFactory;
    }
 
    public function readCsv($csvFilePath)
    {
        $rootDirectory = $this->directoryList->getPath('media');
        $csvFile = $rootDirectory . "/" . $csvFilePath;
        $this->_logger->info($csvFile);
        try {
            if ($this->file->isExists($csvFile)) {
                //set delimiter, for tab pass "\t"
                $this->csv->setDelimiter(",");
                //get data as an array
                $data = $this->csv->getData($csvFile);
                if (!empty($data)) {
                    // ignore first header column and read data
                    foreach ($data as $key => $value) {
                        $productPosition = trim($value['0']);
                        $productId = trim($value['1']);
                        //and so on.
                        $this->_logger->info("Position: " . $productPosition);
                        $this->_logger->info("Value: " . $productId);
                        
                        $this->changeProductPosition($productId, $productPosition);
                    }
                }
            } else {
                $this->_logger->info('Csv file not exist');
                return __('Csv file not exist');
            }
        } catch (FileSystemException $e) {
            $this->_logger->info($e->getMessage());
        }
    }
    
    private function changeProductPosition($productId, $newPosition)
    {
        $product = $this->_productFactory->create()->load($productId); //SKU:END-ADU-EGS
        // Get the category ID of the new product.
        $categoryIds = $product->getCategoryIds();
        
        $categoryId = 65; //Special Effects Lens;
        
        $this->_logger->info("Category ID: " . $categoryId);
        $category = $this->categoryFactory->create()->load($categoryId);
        $products = $category->getProductsPosition();
        $this->_logger->info("Products Position: " . json_encode($products));
        $products[$productId] = $newPosition;
        $category->setPostedProducts($products);
        $category->save();
        
        /*foreach($categoryIds as $categoryId)
        {
            $this->_logger->info("Category ID: " . $categoryId);
            $category = $this->categoryFactory->create()->load($categoryId);
            $products = $category->getProductsPosition();
            //$this->_logger->info("Products Position: " . json_encode($products));
            $products[$productId] = $newPosition;
            $category->setPostedProducts($products);
            $category->save();
        }*/
    }
}