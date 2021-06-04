<?php
namespace Ewave\Banner\Model\ResourceModel\Banner;

use Ewave\Banner\Model\ResourceModel\Attributes as AttributesResourceModel;
use Ewave\Banner\Model\ResourceModel\Video as VideoResource;
use Ewave\Banner\Model\Image\ImageSerializer;

/**
 * Class Collection
 * @package Ewave\Banner\Model\ResourceModel\Banner
 */
class Collection extends \Magento\Banner\Model\ResourceModel\Banner\Collection
{
    const ADD_ROLES_COLUMN_FLAG = 'add_roles_column';

    /**
     * @var AttributesResourceModel
     */
    protected $attributesResourceModel;

    /**
     * @var VideoResource
     */
    protected $videoResource;

    /**
     * @var ImageSerializer
     */
    protected $imageSerializer;

    /**
     * @var \Ewave\Banner\Helper\Image\Config
     */
    protected $mediaConfig;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param AttributesResourceModel $attributesResourceModel
     * @param VideoResource $videoResource
     * @param ImageSerializer $imageSerializer
     * @param \Ewave\Banner\Helper\Image\Config $config
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        AttributesResourceModel $attributesResourceModel,
        VideoResource $videoResource,
        ImageSerializer $imageSerializer,
        \Ewave\Banner\Helper\Image\Config $config,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->attributesResourceModel = $attributesResourceModel;
        $this->videoResource = $videoResource;
        $this->imageSerializer = $imageSerializer;
        $this->mediaConfig = $config;
    }

    /**
     * @return $this|\Magento\Banner\Model\ResourceModel\Banner\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag(self::ADD_ROLES_COLUMN_FLAG)) {
            $this->addRolesToCollection();
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addRolesColumn()
    {
        $this->setFlag(self::ADD_ROLES_COLUMN_FLAG, true);
        return $this;
    }

    /**
     * @return $this
     */
    public function addRolesToCollection()
    {
        $bannerIds = $this->getColumnValues('banner_id');

        if (!empty($bannerIds)) {
            foreach ($this as $item) {
                $roles = '';
                $data = $this->attributesResourceModel->getBannerAttributes($item);
                if (!empty($data['images'])) {
                    $roles = $this->prepareRoles($item, $data['images']);
                }
                $item->setRoles($roles);
            }
        }
        return $this;
    }

    /**
     * @param \Ewave\Banner\Preference\Magento\Banner\Model\BannerModel $item
     * @param array $images
     * @return string
     * @throws \Ewave\Banner\Model\Image\ImageSerializationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareRoles($item, $images)
    {
        $array = [];
        $bannerImages = $this->imageSerializer->unserialize($images);
        $this->videoResource->setBannerSelectProcessed(false);
        $videos = $this->videoResource->getBannerVideos($item->getId(), true);
        $videoRoleIds = [];
        if (!empty($videos)) {
            foreach ($videos as $video) {
                $roleIds = !empty($video['video_role_ids']) ? $video['video_role_ids'] : [];
                if ($roleIds && !is_array($roleIds)) {
                    $roleIds = explode(',', $roleIds);
                }
                $videoRoleIds = array_merge($videoRoleIds, $roleIds);
            }
        }

        $srcSets = $this->mediaConfig->getAllSrcSets();

        foreach ($srcSets as $roleCode => $roleTitle) {
            if (!isset($bannerImages[$roleCode]) && !in_array($roleCode, $videoRoleIds)) {
                continue;
            }
            $array[$roleCode] = $roleTitle;
            unset($bannerImages[$roleCode]);
        }
        return !empty($array)? implode(',', $array) : '';
    }
}
