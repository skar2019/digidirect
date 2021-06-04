<?php
namespace Ewave\ProductAttachment\Ui\DataProvider\Attachment\Form;

use Ewave\ProductAttachment\Model\Registry\Constants;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use Ewave\ProductAttachment\Model\Attachment;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class CategoryDataProvider
 * @package Ewave\Faq\Ui\DataProvider\Category\Form
 */
class AttachmentDataProvider extends AbstractDataProvider
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * AttachmentDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param Registry $registry
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        Registry $registry,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $attachmentCollectionFactory->create();
        $this->registry = $registry;
        $this->pool = $pool;
    }

    /**
     * Used to be able modify data
     *
     * @return array
     */
    public function getData()
    {
        /** @var Attachment $model */
        $model = $this->registry->registry(Constants::CURRENT_ATTACHMENT_ITEM);
        $this->data[$model->getId()] = $model->getData();
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }
}
