<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Api;

/**
 * @api
 */
interface SliderRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\BannerSlider\Api\Data\SliderInterface $slider
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function save(\Amasty\BannerSlider\Api\Data\SliderInterface $slider);

    /**
     * Get by id
     *
     * @param int $id
     * @param int $store
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id, int $store = 0);

    /**
     * Delete
     *
     * @param \Amasty\BannerSlider\Api\Data\SliderInterface $slider
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\BannerSlider\Api\Data\SliderInterface $slider);

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
