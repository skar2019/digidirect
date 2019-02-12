<?php
namespace Ewave\Collect\Api\Data;

/**
 * Post Code Interface.
 *
 * @package Ewave\Collect\Api\Data
 */
interface PostCodeInterface
{
    const POST_CODE_TABLE = 'ewave_collect_post_code_attribute';
    const TABLE_COLUMN_ID = 'entity_id';
    const TABLE_COLUMN_POST_CODE = 'post_code';
    const TABLE_COLUMN_LOCALITY = 'locality';
    const TABLE_COLUMN_STATE = 'state';
    const TABLE_COLUMN_COMMENTS = 'comments';
    const TABLE_COLUMN_CATEGORY = 'category';
    const TABLE_COLUMN_LONGITUDE = 'longitude';
    const TABLE_COLUMN_LATITUDE = 'latitude';

    /**
     * Get Post Code Place Logitude
     *
     * @return string
     */
    public function getLongitude();

    /**
     * Get Post Code Place Latitude
     *
     * @return string
     */
    public function getLatitude();
}
