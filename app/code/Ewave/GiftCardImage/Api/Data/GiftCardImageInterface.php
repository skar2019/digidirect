<?php
namespace Ewave\GiftCardImage\Api\Data;

/**
 * @api
 */
interface GiftCardImageInterface
{
    const PUBLIC_KEY = 'ewave_giftcard_image';

    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'giftcard_image_id';

    const TITLE = 'title';

    const STATUS = 'status';

    const IMAGE = 'image';

    /**
     * Gift Card Image id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Gift Card Image id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getImage();

    /**
     * @param string $title
     * @return $this
     */
    public function setImage($title);
}
