<?php
namespace Ewave\Migration\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;

/**
 * Class MigrationProcessor
 */
class MigrationProcessor
{
    const IMAGE_DIR = 'kayweb_migration/orig/';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var \Ewave\Migration\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var array
     */
    protected $optionIds = [];

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_filesystemDriver;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     * @since 101.0.0
     */
    protected $mediaDirectory;

    /**
     * MigrationProcessor constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Ewave\Migration\Helper\Data $helper
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Ewave\Migration\Helper\Data $helper,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->helper = $helper;
        $this->objectFactory = $objectFactory;
        $this->_filesystemDriver = $filesystemDriver;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * @param $data
     * @param $output
     * @param int $offset
     * @param null $limit
     */
    public function process($data, $output, $offset = 0, $limit = null)
    {
        $output->writeln('Starting a migration process: ');
        $errorsIds = [];

        if (!empty($offset) || !empty($limit)) {
            $data['RECORDS'] = array_slice($data['RECORDS'], $offset, $limit);
        }

        if (count($data['RECORDS'])) {
            foreach ($data['RECORDS'] as $record) {
                $row = $this->objectFactory->create();
                $row->setData($record);

                $sku = !empty($row->getData('pronto_code')) ? $row->getData('pronto_code') : $this->helper->prepareSku($row);
                try {
                    $product = $this->productRepository->get($sku, false, 0);
                } catch (NoSuchEntityException $e) {
                    $product = $this->productFactory->create();
                }

                /**
                 * @var \Magento\Catalog\Api\Data\ProductInterface $product
                 */
                $product->setSku($sku);
                $product->setStoreId(0);
                $productName = $this->helper->htmlEntityDecode($row->getData('Name')) ?: 'Migration ' . $row->getData('ID');
                $product->setName($productName);
                $product->setAttributeSetId(4);
                $product->setStatus((int) $row->getData('Enabled') ? 1 : 2);
                $product->setTypeId('simple');
                $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                $product->setTaxClassId(0);

                if ($row->getData('brand_name')) {
                    $product->setBrand($this->getOptionIdByLabel('brand', $this->helper->htmlEntityDecode($row->getData('brand_name')), $product));
                }

                $product->setModel($this->helper->htmlEntityDecode($row->getData('Model')));
                $product->setData('barcode1', $row->getData('gtin'));
                $product->setApn($row->getData('mpn'));
                $product->setDescription($this->helper->htmlEntityDecode($row->getData('Description')));
                $product->setShortDescription($this->helper->htmlEntityDecode($row->getData('product_overview')));
                $product->setCost($row->getData('SupplierPrice'));

                $product->setMetaKeyword($row->getData('MetaKeywords'));
                $product->setMetaDescription($row->getData('MetaDescription'));

                $product->setStockData(
                    array(
                        'backorders' => $row->getData('pre_order') ? \Ewave\PreOrder\Helper\Data::BACKORDERS_PREORDER_OPTION : 1,
                        'use_config_backorders' => $row->getData('pre_order') ? 0 : 1,
                        'qty' => 0
                    )
                );

                if ($row->getData('kit_option')) {
                    $product->setStockStatus($this->getOptionIdByLabel('stock_status', 'K', $product));
                }
                $product->setWhatsInTheBox($this->helper->htmlEntityDecode($row->getData('box_package')));
                $product->setSpecification($this->helper->htmlEntityDecode($row->getData('custom_spec_text')));
                $product->setPrice(0);

                $images = $this->helper->getImagesList($row->getData('Images'));
                $mainImageIndex = $this->helper->getMainImageIndex($images);

                if (!empty($images)) {
                    foreach ($images as $imageIndex => $image) {
                        $attributes = $imageIndex == $mainImageIndex ? array('image', 'small_image', 'thumbnail') : null;
                        try {
                            $imagePath = $this->prepareImageName($image[0], $row->getData('ID'));
                            $product->addImageToMediaGallery($imagePath, $attributes, false, false);
                        } catch (\Exception $e) {}
                    }
                }

                try {
                    try {
                        $this->productRepository->save($product);
                        $output->write('.');
                    } catch (UrlAlreadyExistsException $e) {
                        $product->setUrlKey($row->getData('url') . '-' . time());
                        $output->write('0');
                        $this->productRepository->save($product);
                    }
                } catch (\Exception $e) {
                    $output->write('x');
                    $errorsIds[$row->getData('ID')] = get_class($e) . ': ' . $e->getMessage();
                }
            }
        }

        if (!empty($errorsIds)) {
            $output->writeln('');
            $output->writeln('Errors: ');

            foreach ($errorsIds as $key => $error) {
                $output->writeln('ID: ' . $key . ' - ' . $error);
            }
        }
    }

    /**
     * @param string $attributeCode
     * @param string $label
     * @param ProductInterface $product
     * @return int
     */
    protected function getOptionIdByLabel(string $attributeCode, string $label, ProductInterface $product):int
    {
        if (!isset($this->optionIds[$attributeCode][$label])) {
            $optionId = $this->helper->getAttributeOptionId($attributeCode, $label, $product);
            if (!$optionId) {
                $optionId = $this->helper->addAttributeOptionId($attributeCode, $label, $product);
            }
            $this->optionIds[$attributeCode][$label] = $optionId;
        }

        return $this->optionIds[$attributeCode][$label];
    }

    /**
     * @param string $imageName
     * @param string $productId
     * @return string
     */
    protected function prepareImageName(string $imageName, string $productId):string
    {
        $prefix = self::IMAGE_DIR . $productId . '_';

        $pathParts = pathinfo($imageName);

        if (strpos($pathParts['filename'], '.') !== false) {
            $newImageName = str_replace('.', '_', $pathParts['filename']) . '.' . $pathParts['extension'];

            try {
                $this->_filesystemDriver->copy(
                    $this->mediaDirectory->getAbsolutePath() . $prefix . $imageName,
                    $this->mediaDirectory->getAbsolutePath() . $prefix . $newImageName
                );
                $imageName = $newImageName;
            } catch (\Magento\Framework\Exception\FileSystemException $e) {
                // nothing
            }
        }

        return $prefix . $imageName;
    }
}
