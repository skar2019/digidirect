<?php

namespace Ewave\ProductOverlay\Api;

use Ewave\ProductOverlay\Api\Data\OverlayInterface;
use Ewave\ProductOverlay\Api\Data\OverlaySearchResultsInterface;

/**
 * Overlay CRUD interface.
 * @api
 */
interface OverlayRepositoryInterface
{
    /**
     * Save overlay.
     *
     * @param \Ewave\ProductOverlay\Api\Data\OverlayInterface $overlay
     * @return \Ewave\ProductOverlay\Api\Data\OverlayInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(OverlayInterface $overlay);

    /**
     * Retrieve overlay.
     *
     * @param int $overlayId
     * @return \Ewave\ProductOverlay\Api\Data\OverlaySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($overlayId);

    /**
     * Retrieve overlays matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ewave\ProductOverlay\Api\Data\OverlaySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete overlay.
     *
     * @param \Ewave\ProductOverlay\Api\Data\OverlayInterface $overlay
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(OverlayInterface $overlay);

    /**
     * Delete overlay by ID.
     *
     * @param int $overlayId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($overlayId);
}
