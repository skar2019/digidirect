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
                    if ($option->getTitle() == 'DigiProtect') { //&& $option->getGroupByType() == ProductCustomOptionInterface::OPTION_TYPE_FIELD
                        $exist = true;
                    }
                }
            
                if (!$exist) {
                    
                        $optionsArray = [   
                            'title' => 'DigiProtect',
                            'type' => 'radio',
                            'is_require' => 0,
                            'sort_order' => 1,
                            'values' => [
                                [
                                    'title' => '+ 1 year',
                                    'price' => 100,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 1,
                                ],
                                [
                                    'title' => '+ 2 years',
                                    'price' => 150,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 2,
                                ],
                                [
                                    'title' => '+ 3 years',
                                    'price' => 180,
                                    'price_type' => 'fixed',
                                    'sku' => '',
                                    'sort_order' => 3,
                                ]
                            ]
                        ]; 
                        
                        
                    try {
                        
                        $option = \Magento\Framework\App\ObjectManager::getInstance()->create(
                            '\Magento\Catalog\Model\Product\Option'
                        );
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
                        //throw new \Magento\Framework\Exception\NoSuchEntityException(__('Something went wrong'));
                    }
                }
            }
            
            
        
    }
    
    public function deleteCustomOption($id){
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $id]);

        foreach ($collection as $item) {

            $this->_productRepositoryInterface->getById($item->getId());
            $this->_productRepository->load($item->getId());
            $product = $this->_productRepository;

            if ($product->getOptions()) {
                foreach ($product->getOptions() as $opt) {
                    $opt->delete();
                }
                $product->setHasOptions(0)->save();
            } 
        }
    }

}