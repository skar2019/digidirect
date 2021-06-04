<?php
namespace Ewave\Faq\Ui\DataProvider\Faq\Form;

use Ewave\Faq\Model\Faq;
use Ewave\Faq\Model\ResourceModel\Faq\CollectionFactory as FaqCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class FaqDataProvider
 * @package Ewave\Faq\Ui\DataProvider\Faq\Form
 */
class FaqDataProvider extends AbstractDataProvider
{

    /**
     * FaqDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param FaqCollectionFactory $faqCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FaqCollectionFactory $faqCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $faqCollectionFactory->create();
    }

    /**
     * Used to be able modify data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        /**
         * @var Faq $faqItem
         */
        foreach ($items as $faqItem) {
            $result = $faqItem->getData();
            $this->data[$faqItem->getId()] = $result;
        }
        return $this->data;
    }
}
