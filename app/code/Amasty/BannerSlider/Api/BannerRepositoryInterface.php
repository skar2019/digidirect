<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Api;

interface BannerRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\BannerSlider\Api\Data\BannerInterface $banner
     *
     * @return \Amasty\BannerSlider\Api\Data\BannerInterface
     */
    public function save(\Amasty\BannerSlider\Api\Data\BannerInterface $banner);

    /**
     * @param int $id
     * @param int $store
     * @return \Amasty\BannerSlider\Model\Banner
     */
    public function getById(int $id, int $store = 0);

    /**
     * Delete
     *
     * @param \Amasty\BannerSlider\Api\Data\BannerInterface $banner
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\BannerSlider\Api\Data\BannerInterface $banner);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
