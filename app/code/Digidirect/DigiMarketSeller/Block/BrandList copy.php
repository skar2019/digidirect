<?php

/**
 * Get all Brands list
 * @return array
 */



namespace Digidirect\DigiMarketSeller\Block;

class BrandList extends \Magento\Framework\View\Element\Template
{

    // country_of_manufacture


    protected $eavAttributeRepository;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository
    ){
        parent::__construct($context);
        $this->eavAttributeRepository = $eavAttributeRepository;
     }
  
    public function getManufacturerListing(){
        $attributes = $this->eavAttributeRepository->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,'manufacturer_name');
        $options = $attributes->getSource()->getAllOptions(false);
        return $options;
    }
}