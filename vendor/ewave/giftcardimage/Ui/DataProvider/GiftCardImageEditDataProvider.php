<?php
namespace Ewave\GiftCardImage\Ui\DataProvider;

use Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\Grid\CollectionFactory;
use Ewave\GiftCardImage\Helper\Image as ImageHelper;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\File\Size;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Backend\Model\Session;

/**
 * Class GiftCardImageEditDataProvider
 * @package Ewave\GiftCardImage\Ui\DataProvider
 */
class GiftCardImageEditDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var Size
     */
    protected $size;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param Session $session
     * @param ImageHelper $imageHelper
     * @param Size $size
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        Session $session,
        ImageHelper $imageHelper,
        Size $size,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->session = $session;
        $this->imageHelper = $imageHelper;
        $this->size = $size;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * @param array $meta
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareMeta($meta)
    {
        $result = [];
        $result['general']['children']['image']['arguments']['data']['config']['maxFileSize'] =
            $this->size->getMaxFileSize();

        $result['general']['children']['image']['arguments']['data']['config']['notice'] =
            __('Allowed file types: png, gif, jpg, jpeg, apng. Not all browsers support all these formats!') .
            '<br/>' .
            __('The Max file size is %1Mb.', $this->size->getMaxFileSizeInMb());

        $meta = array_replace_recursive($meta, $result);
        return $meta;
    }

    /**
     * Get collection
     * @return \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\Grid\Collection
     */
    public function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->collectionFactory->create();
        }
        return $this->collection;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        $items = $this->getCollection()->getItems();
        /** @var \Ewave\GiftCardImage\Model\GiftCardImage $giftCardImage */
        foreach ($items as $giftCardImage) {
            $this->imageHelper->addImagesInfo($giftCardImage);
            $this->loadedData[$giftCardImage->getGiftcardImageId()] = $giftCardImage->getData();
        }

        $formData = $this->session->getGiftCardImageData();
        if (!empty($formData)) {
            $newOption = $this->collection->getNewEmptyItem();
            $newOption->addData($formData);
            $this->loadedData[$newOption->getId()] = $newOption->getData();
            $this->session->unsGiftCardImageData();
        }

        return $this->loadedData;
    }
}
