<?php
namespace Ewave\OutOfStockNotif\Model;

use Magento\ProductAlert\Model\StockFactory;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class OutOfStockRepository
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StockFactory
     */
    protected $stockFactory;

    /**
     * @var CollectionFactory
     */
    protected $stockCollectionFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param StockFactory $stockFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockFactory $stockFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->stockFactory = $stockFactory;
        $this->stockCollectionFactory = $collectionFactory;
    }

    /**
     * @param string $email
     * @param int $productId
     * @param int $websiteId
     * @param int|null $customerId
     * @return bool
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function subscribe($email, $productId, $websiteId, $customerId = null)
    {
        if (!\Zend_Validate::is($email, 'EmailAddress')) {
            throw new LocalizedException(__('Email address is not valid'));
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->productRepository->getById($productId);
        if (!$product->getId()) {
            throw new LocalizedException(__('Product does not exist.'));
        }

        /* @var $collection \Magento\ProductAlert\Model\ResourceModel\Stock\Collection */
        $collection = $this->stockCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('product_id', $product->getId());

        if (!$collection->getSize()) {
            /** @var \Magento\ProductAlert\Model\Stock */
            $this->stockFactory->create()
                ->setCustomerId($customerId)
                ->setEmail($email)
                ->setProductId($product->getId())
                ->setWebsiteId($websiteId)
                ->save();

            return true;
        }

        $isSubscribed = false;
        foreach ($collection as $alert) {
            if ($alert->getStatus()) {
                $alert->setStatus(0);
                $alert->save();
                $isSubscribed = true;
            }
        }

        return $isSubscribed;
    }
}
