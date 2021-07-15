<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\Product;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;

class CategoryUpdate extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_productCollectionFactory;
    
    protected $categoryFactory;
    
    protected $categoryLinkRepository;
            
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            Product $helper,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
            \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
            \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku $sourceDataBySku,
            \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            CategoryLinkRepositoryInterface $categoryLinkRepository,
            array $data = [])
    {
            $this->helper = $helper;
            $this->_productCollectionFactory = $productCollectionFactory;
            $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
            $this->sourceItemFactory = $sourceItemFactory;
            $this->sourceDataBySku = $sourceDataBySku;
            $this->productStatus = $productStatus;
            $this->categoryFactory = $categoryFactory;
            $this->categoryLinkRepository = $categoryLinkRepository;
            return parent::__construct($context);
    }

    public function execute()
    {
        if(isset($_GET["cat"])){
            $categoryId = $_GET["cat"];
        }
        set_time_limit(300);
        //$categoryId = 339; //Paper ID
        $category = $this->categoryFactory->create()->load($categoryId);
        $categoryProducts = $category->getProductCollection()
                ->addAttributeToSelect('*')
                ->setPageSize(100);
        
        $isProductUnassigned = false;
        foreach ($categoryProducts as $product) {
            $sku = $product->getSku();

            try 
            {
                $isProductUnassigned = $this->categoryLinkRepository->deleteByIds($categoryId, $sku);
                echo $sku." - ".$isProductUnassigned."<br>";
            } catch (Exception $ex) {
                echo $ex->getMessage();
                echo $sku." - ".$isProductUnassigned."<br>";
                continue;
            }
            
            
        }

        exit();
    }
}