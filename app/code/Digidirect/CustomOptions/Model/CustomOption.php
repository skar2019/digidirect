<?php
namespace Digidirect\CustomOptions\Model;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
 
class CustomOption extends \Magento\Framework\Model\AbstractModel
{
    protected $_productOptions;
    protected $_productRepositoryInterface;
    protected $_productRepository;
    protected $_giftCardHelper;
    protected $_productOptionFactory;
 
    public function __construct(
        \Magento\Catalog\Model\Product\Option $productOptions,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Model\Product $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
    ){
        $this->_productOptions = $productOptions;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_productRepository = $productRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productOptionFactory = $productOptionFactory;
    }
 
    public function saveCustomOption(){

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter( 'price' , array('gt' => 100));
        $collection->addAttributeToFilter('type_id', array('eq' => 'simple'));
        $collection->addAttributeToFilter('brand', array('neq' => 'LEICA'));
        $collection->addAttributeToFilter('stock_group', array('neq' => 'D1A1'));
        //$collection->addCategoriesFilter(['in' => $id]);
        //might check 'stock_status' => string '171'

        foreach ($collection as $item) {


            $this->_productRepositoryInterface->getById($item->getId());
            $this->_productRepository->load($item->getId());

            $price = $this->_productRepository->getPrice();
            $sku = $this->_productRepository->getSku();
            $exist = false;
            
            //check if the custom option exists
            foreach ($this->_productRepository->getOptions() as $option) {
                if($option)
                {
                    if ($option->getTitle() == 'digiProtect') { //&& $option->getGroupByType() == ProductCustomOptionInterface::OPTION_TYPE_FIELD
                        $exist = true;
                    }
                }
                
            }

            if (!$exist) {

                if($price > 1000 && $price < 2000)
                {
                    $optionsArray = [   
                        'title' => 'digiProtect',
                        'type' => 'radio',
                        'is_require' => 0,
                        'sort_order' => 1,
                        'values' => [
                            [
                                'title' => '+ 3 years',
                                'price' => 134.95,
                                'price_type' => 'fixed',
                                'sku' => '138939',
                                'sort_order' => 1,
                            ]
                        ]
                    ]; 
                }
                else if($price > 2000)
                {
                    $optionsArray = [   
                        'title' => 'digiProtect',
                        'type' => 'radio',
                        'is_require' => 0,
                        'sort_order' => 1,
                        'values' => [
                            [
                                'title' => '+ 3 years',
                                'price' => 284.95,
                                'price_type' => 'fixed',
                                'sku' => '138940',
                                'sort_order' => 1,
                            ]
                        ]
                    ]; 
                }
                else 
                {
                    $optionsArray = [   
                        'title' => 'digiProtect',
                        'type' => 'radio',
                        'is_require' => 0,
                        'sort_order' => 1,
                        'values' => [
                            [
                                'title' => '+ 3 years',
                                'price' => 89.95,
                                'price_type' => 'fixed',
                                'sku' => '138939',
                                'sort_order' => 1,
                            ]
                        ]
                    ];    
                }

                try {
                    echo "<br>try - ".$sku;
                    $option = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Catalog\Model\Product\Option');
                    $this->_productRepository->setHasOptions(1);
                    $this->_productRepository->setCanSaveCustomOptions(true);
                    $product = $this->_productRepository;
                    $option->setProductId($this->_productRepository->getData('row_id'))
                        ->setStoreId($product->getStoreId())
                        ->addData($optionsArray);
                    $option->save();
                    $product->addOption($option);
                    $product->save();

                } catch (\Exception $exception) {
                    echo $exception;
                    //throw new \Magento\Framework\Exception\NoSuchEntityException(__('Something went wrong'));
                }
            }


        }
        echo "added custom options";

    }
    
    public function deleteCustomOption(){

        $x = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $products = $objectManager->get('\Magento\Catalog\Model\Product')->getCollection();
        foreach ($products as $product) {
            $productId = $product->getId();
            //$product = $objectManager->get('\Magento\Catalog\Model\Product')->load($product->getId());
            $product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
            if ($product->getOptions()) {
                echo "<br /> delete - " .$product->getId();
                foreach ($product->getOptions() as $opt) {
                   $opt->delete();
                }
                $x++;
            }
            
        }
        echo "delete custom option";

    }

}