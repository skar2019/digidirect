<?php

namespace Ewave\Banner\Model\ResourceModel;

use Ewave\Banner\Helper\IssetTrait;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Banner\Model\Banner as BannerModel;

class Video extends AbstractDb
{
    use IssetTrait;

    const BANNER_VIDEO_TABLE = 'ewave_banner_video';
    const SIZE_COLUMN = 'video_size';

    /**
     * @var bool
     */
    protected $bannerSelectProcessed = false;

    /**
     * @var array
     */
    protected $bannerVideosById = [];

    /**
     * Set main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_banner_video', 'video_id');
    }

    /**
     * @param bool $flag
     * @return void
     */
    public function setBannerSelectProcessed($flag)
    {
        $this->bannerSelectProcessed = $flag;
    }

    /**
     * @param BannerModel $banner
     * @return BannerModel
     */
    public function saveVideo(BannerModel $banner)
    {
        $video = $banner->getVideo();
        if (!empty($video)) {
            $connection = $this->getConnection();
            $deleted = $this->getByKey($video, 'deleted', []);
            $this->deleteVideo($deleted);
            $toSave = $this->getByKey($video, 'uploaded', []);
            foreach ($toSave as $data) {
                $data['banner_id'] = $banner->getId();
                if (!empty($data['video_id'])) {
                    $connection->update(
                        $this->getMainTable(),
                        $data,
                        $connection->quoteInto('video_id = ?', $data['video_id'])
                    );
                } else {
                    $connection->insertOnDuplicate(
                        $this->getMainTable(),
                        $data
                    );
                }
            }
        }

        return $banner;
    }

    /**
     * Delete videos
     *
     * @param array $ids
     * @return void
     */
    public function deleteVideo(array $ids = [])
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            $this->getConnection()->quoteInto('video_id IN (?)', $ids)
        );
    }

    /**
     * @param int $bannerId
     * @param bool $forceSingle
     * @return array|mixed
     */
    public function getBannerVideos($bannerId, $forceSingle = false)
    {
        if ($this->bannerSelectProcessed) {
            return $this->getByKey($this->bannerVideosById, $bannerId, []);
        }
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), ['*']);
        if ($forceSingle) {
            $select->where($connection->quoteInto('banner_id = ?', $bannerId));
        }
        $videos = $connection->fetchAll($select);
        $this->setBannerSelectProcessed(true);
        if (!empty($videos)) {
            foreach ($videos as $video) {
                $bannerId = $this->getByKey($video, 'banner_id', null);
                $this->bannerVideosById[$bannerId][] = $video;
            }
        }
        return $this->getByKey($this->bannerVideosById, $bannerId, []);
    }

    /**
     * @return string
     */
    public static function getEntityTableName()
    {
        return static::BANNER_VIDEO_TABLE;
    }
}
