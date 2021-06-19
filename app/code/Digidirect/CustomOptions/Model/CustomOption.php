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
 
    public function saveCustomOption($id){

            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addCategoriesFilter(['in' => $id]);
            
            foreach ($collection as $item) {
                
                $this->_productRepositoryInterface->getById($item->getId());
                $this->_productRepository->load($item->getId());
                //echo $this->_productRepository->getData('row_id');
                            
                $exist = false;
                //check if the custom option exists
                foreach ($this->_productRepository->getOptions() as $option) {
                    if ($option->getTitle() == 'Warranty') { //&& $option->getGroupByType() == ProductCustomOptionInterface::OPTION_TYPE_FIELD
                        $exist = true;
                    }
                }
            
                if (!$exist) {
                        
                        //$this->_productOptions->unsetOptions();
                        
                        $optionsArray = [   
                            'title' => 'Warranty',
                            'type' => 'radio',
                            'is_require' => 0,
                            'sort_order' => 1,
                            'values' => [
                                [
                                    'title' => '1 year warranty',
                                    'price' => 100,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 1,
                                ],
                                [
                                    'title' => '2 years warranty',
                                    'price' => 150,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 2,
                                ],
                                [
                                    'title' => '3 years warranty',
                                    'price' => 180,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 3,
                                ]
                            ]
                        ]; 
                        
                        
                    try {
                        
//                        $option = $this->_productOptions;
//                        $this->_productRepository->setHasOptions(1);
//                        $this->_productRepository->setCanSaveCustomOptions(true);
//                        //foreach ($optionsArray as $arrayOption) {
//                            echo "Start : " . $this->_productRepository->getId() . "Row : " . $this->_productRepository->getData('row_id') ."<br>";
//                            
//                            //$option = $this->productOptionFactory->create();
//                            $option->setProductId($this->_productRepository->getData('row_id'));
//                           
//                            $option->setStoreId($this->_productRepository->getStoreId());
//                             
//                            $option->addData($optionsArray);
//                            //$option->save();
//                            $this->_productRepository->addOption($option);
//                            $this->_productRepository->save();    
//                            
//                            echo "End : " . $this->_productRepository->getId() . "Row : " . $this->_productRepository->getData('row_id') ."<br>";
//                        //}
                            //Mage::getSingleton('catalog/product_option')->unsetOptions();
                            
                            //$option = $this->_productOptions;
                            //$option->unsetOptions();
                        $option = \Magento\Framework\App\ObjectManager::getInstance()->create(
                            '\Magento\Catalog\Model\Product\Option'
                        );
                            $product = $this->_productRepository;
                            $option->setProductId($this->_productRepository->getData('row_id'))
                                ->setStoreId($product->getStoreId())
                                ->addData($optionsArray);
                            $option->save();
                            $product->addOption($option);
                            
                            //$option->unsetOptions();
                        
                    } catch (\Exception $exception) {
                        //throw new \Magento\Framework\Exception\NoSuchEntityException(__('Something went wrong'));
                        //echo "Caught exception : " . $exception->getMessage();
                    }
                }
                //$this->_productRepository->save();  
            }
            
            
        
    }

}