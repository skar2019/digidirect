<?php
namespace Ewave\GiftCardImage\Model;

use Magento\Framework\Model\AbstractModel;
use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;

class GiftCardImage extends AbstractModel implements GiftCardImageInterface
{
    /**
     * GiftCardImage constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_init('Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage');
    }

    /**
     * @return int|null
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->_getData(self::IMAGE);
    }

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
        return $this;
    }
}
